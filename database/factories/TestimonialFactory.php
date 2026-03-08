<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'country' => $this->faker->country(),
            'message' => $this->faker->paragraph(2),
            'rating' => $this->faker->numberBetween(4, 5),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'is_approved' => true,
            'source' => $this->faker->randomElement(['internal', 'google']),
            'avatar' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
