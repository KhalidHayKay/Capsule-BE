<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Waitlist>
 */
class WaitlistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email'         => $this->faker->unique()->safeEmail(),
            'name'          => $this->faker->optional(0.8)->name(),
            'referral_code' => $this->faker->optional()->numerify('REF_#####'),
        ];
    }
}
