<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tour;
use App\Models\Booking;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour_id' => Tour::inRandomOrder()->first()->id ?? Tour::factory(),
            'booking_id' => Booking::inRandomOrder()->first()->id ?? Booking::factory(),
            'customer_id' => Customer::inRandomOrder()->first()->id ?? Customer::factory(),
            'rating' => fake()->numberBetween(3, 5),
            'comment' => fake()->paragraph(),
            'is_verified' => true,
            'is_approved' => true,
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
