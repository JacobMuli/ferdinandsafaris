<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Tour;
use App\Models\Accommodation;
use App\Models\VehicleType;
use App\Models\ParkLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DynamicTourPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_price_with_dynamic_factors()
    {
        // Setup Data
        $tour = Tour::create([
            'name' => 'Safari Adventure',
            'slug' => 'safari-adventure',
            'description' => 'Test Tour',
            'duration_days' => 5,
            'price_per_person' => 1000, // Base Service Fee
            'child_price' => 500,
            'category' => 'wildlife',
            'location' => 'Kenya',
            'min_group_size' => 2,
        ]);

        $accommodation = Accommodation::create([
            'name' => 'Luxury Lodge', 
            'slug' => 'luxury-lodge',
            'price_per_night_standard' => 200,
            'total_rooms' => 10
        ]);
        
        $vehicleType = VehicleType::create([
            'name' => 'Land Cruiser',
            'category' => 'land_cruiser',
            'manufacturer' => 'Toyota',
            'model' => 'Land Cruiser 70',
            'default_capacity' => 7,
            'base_daily_rate' => 150,
            'is_active' => true
        ]);

        $park = ParkLocation::create([
            'name' => 'Mara',
            'slug' => 'mara',
            'type' => 'National Reserve',
            'country' => 'Kenya',
            'description' => 'Test Park',
            'entry_fee_adult' => 80,
            'entry_fee_child' => 40
        ]);
        
        // Link accommodation and park to tour
        $tour->accommodations()->attach($accommodation->id, ['nights' => 4, 'price_per_night' => 200]);
        $tour->parkLocations()->attach($park->id, ['days' => 4]);

        // Calculate Price based on:
        // 2 Adults
        // Accommodation: $200 x 4 nights x 2 people = $1600
        // Vehicle: $150 x 5 days = $750
        // Park: $80 x 4 days x 2 people = $640
        // Base: $1000 x 2 = $2000
        // Total Expected: 2000 + 1600 + 750 + 640 = $4990

        $options = [
            'accommodation_id' => $accommodation->id,
            'vehicle_type_id' => $vehicleType->id,
            'resident_status' => 'non_resident'
        ];

        $pricing = $tour->calculatePrice('individual', 2, 0, $options);

        // Assertions
        $this->assertEquals(2000, $pricing['breakdown']['base_tour_cost']);
        $this->assertEquals(1600, $pricing['breakdown']['accommodation_cost']);
        $this->assertEquals(750, $pricing['breakdown']['vehicle_cost']);
        $this->assertEquals(640, $pricing['breakdown']['park_fees']);
        
        $this->assertEquals(4990, $pricing['final_price']);
    }
}
