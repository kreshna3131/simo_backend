<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TwoFactor>
 */
class TwoFactorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => null,
            'two_factor_ip' => '127.0.0.1',
            'two_factor_code' => '111111',
            'two_factor_expires_at' => now()->addHour(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
