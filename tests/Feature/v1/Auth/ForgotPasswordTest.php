<?php

namespace Tests\Feature\v1\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ForgotPasswordTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function sendEmailShouldOk()
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson(route('send-reset-link'), [
            'email' => $user->email
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['status']);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /** @test */
    public function resetPasswordShouldOk()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('reset-password'), [
            'token' => Password::broker()->createToken($user),
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['status']);
    }
}
