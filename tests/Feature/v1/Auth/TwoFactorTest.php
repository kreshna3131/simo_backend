<?php

namespace Tests\Feature\v1\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\TwoFactor;
use Laravel\Sanctum\Sanctum;
use App\Notifications\TwoFactorNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TwoFactorTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function userShouldVerifyTwoFactor()
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);

        Notification::assertSentTo(
            $user,
            TwoFactorNotification::class
        );
        $response->assertStatus(401);
    }

    /** @test */
    public function verifyTwoFactorShouldOk()
    {
        $user = User::factory()->create();
        $twoFactor = TwoFactor::factory()->create([
            'user_id' => $user->id
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('verify-two-factor'), [
            'two_factor_code' => $twoFactor->two_factor_code
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('two_factors', [
            'two_factor_ip' => $twoFactor->two_factor_ip,
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);
    }

    /** @test */
    public function resendTwoFactorShouldOk()
    {
        Notification::fake();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('resend-two-factor'));

        $response->assertOk();
        Notification::assertSentTo($user, TwoFactorNotification::class);
    }
}
