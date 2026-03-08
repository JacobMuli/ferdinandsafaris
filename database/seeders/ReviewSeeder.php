<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Booking;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some completed bookings
        $completedBookings = Booking::where('status', 'completed')->get();
        
        foreach ($completedBookings as $booking) {
            if (rand(0, 1)) { // 50% chance to leave a review
                Review::factory()->create([
                    'booking_id' => $booking->id,
                    'tour_id' => $booking->tour_id,
                    'customer_id' => $booking->customer_id,
                ]);
            }
        }
    }
}
