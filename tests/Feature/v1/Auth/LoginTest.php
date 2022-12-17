<?php

namespace Tests\Feature\v1\Auth;

use App\Models\Setting;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function loginShouldOk()
    {
        $user = User::factory()->create();
        Setting::first()->update(["enabled_2fa" => false]);

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token', 'message']);
    }

    /** @test */
    public function logoutShouldOk()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(route('logout'));

        $response->assertOk();
        $response->assertJsonStructure(['message']);
    }

    /** @test */
    public function blockedUserShouldNotPass()
    {
        $user = User::factory()->create([
            'blocked' => true,
            'blocked_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('logout'));

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => trans('auth.banned')]);
    }
}
