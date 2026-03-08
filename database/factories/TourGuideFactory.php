<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourGuide>
 */
class TourGuideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'license_number' => 'TG-' . fake()->unique()->numerify('#####'),
            'license_expiry_date' => fake()->dateTimeBetween('now', '+2 years'),
            'profile_photo' => null, // Or a placeholder URL if you prefer
            'is_active' => true,
        ];
    }
}
