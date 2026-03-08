<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        $vehicleType = VehicleType::inRandomOrder()->first();

        return [
            // ✅ Correct FK
            'vehicle_type_id' => $vehicleType->id,

            // ✅ Generated registration
            'registration_number' =>
                strtoupper(fake()->randomLetter() . fake()->randomLetter()) .
                ' ' . fake()->numberBetween(100, 999) .
                strtoupper(fake()->randomLetter()),

            'year' => fake()->numberBetween(2018, 2024),
            'color' => fake()->safeColorName(),

            // ✅ Operational / per-unit data only
            'mileage' => fake()->numberBetween(10_000, 180_000),
            'last_service_date' => fake()->dateTimeBetween('-6 months', '-1 month'),
            'next_service_due' => fake()->dateTimeBetween('+1 month', '+3 months'),

            'insurance_company' => fake()->randomElement([
                'Jubilee Insurance',
                'APA Insurance',
                'CIC Insurance',
                'Britam Insurance'
            ]),

            'insurance_policy_number' => 'INS-' . fake()->numerify('######'),
            'insurance_expiry_date' => fake()->dateTimeBetween('+3 months', '+12 months'),
            'road_tax_expiry_date' => fake()->dateTimeBetween('+2 months', '+10 months'),
            'inspection_expiry_date' => fake()->dateTimeBetween('+1 month', '+6 months'),

            'status' => fake()->randomElement([
                'available',
                'available',
                'in_use',
                'maintenance'
            ]),

            'is_available' => fake()->boolean(80),

            'current_location' => fake()->randomElement([
                'Nairobi',
                'Mombasa',
                'Arusha',
                'Nakuru'
            ]),

            'home_base' => 'Nairobi',
        ];
    }
}
