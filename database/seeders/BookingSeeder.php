<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $tours = \App\Models\Tour::pluck('id');
        $customers = \App\Models\Customer::pluck('id');
        
        if ($tours->isEmpty() || $customers->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 50; $i++) {
            \App\Models\Booking::factory()->create([
                'tour_id' => $tours->random(),
                'customer_id' => $customers->random(),
            ]);
        }
    }

}
