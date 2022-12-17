<?php

namespace Tests\Feature\v1\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VerifyEmailTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function userShouldVerifyEmailFirst()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure(['message']);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function verifyEmailShouldOk()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verify-email',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->postJson($verificationUrl);

        $response->assertOk();
        $this->assertNotNull($user->email_verified_at);
    }
}
