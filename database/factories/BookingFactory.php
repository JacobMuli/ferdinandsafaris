<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tour;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'confirmed', 'paid', 'completed', 'cancelled'];
        $status = fake()->randomElement($statuses);
        $adultsCount = fake()->numberBetween(1, 4);
        $childrenCount = fake()->numberBetween(0, 3);
        
        return [
            'tour_id' => Tour::inRandomOrder()->first()->id ?? Tour::factory(),
            'customer_id' => Customer::inRandomOrder()->first()->id ?? Customer::factory(),
            'customer_type' => fake()->randomElement(['individual', 'family', 'group', 'corporate']),
            'tour_date' => fake()->dateTimeBetween('+1 week', '+1 year'),
            'adults_count' => $adultsCount,
            'children_count' => $childrenCount,
            'total_participants' => $adultsCount + $childrenCount,
            'base_price' => fake()->randomFloat(2, 1000, 5000),
            'discount_amount' => 0,
            'tax_amount' => fake()->randomFloat(2, 100, 500),
            'total_amount' => fake()->randomFloat(2, 1200, 6000),
            'status' => $status,
            'payment_status' => in_array($status, ['paid', 'completed']) ? 'paid' : 'pending',
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'confirmed_at' => in_array($status, ['confirmed', 'paid', 'completed']) ? now() : null,
        ];
    }
}
