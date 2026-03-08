<?php

use App\Models\Booking;
use App\Models\Tour;
use App\Models\TourGuide;
use App\Models\Customer;
use App\Models\Vehicle;

// Ensure we have data
if (Tour::count() === 0) {
    echo "No tours found. Creating one...\n";
    Tour::create([
        'name' => 'Safari Adv', 'slug' => 'safari-adv', 'price' => 100, 'duration_days' => 3
    ]); // Simplified
}
if (Customer::count() === 0) {
    echo "No customers found. Creating one...\n";
    Customer::create([
        'first_name' => 'Test', 'last_name' => 'User', 'email' => 'test@example.com'
    ]);
}
if (TourGuide::count() === 0) {
    echo "No Active Guides found. Creating one...\n";
    TourGuide::create([
        'first_name' => 'Guide', 'last_name' => 'One', 'email' => 'guide@example.com',
        'is_active' => true, 'is_available' => true
    ]);
}

$tour = Tour::first();
$customer = Customer::first();

echo "Creating Pending Booking...\n";
$booking = Booking::create([
    'tour_id' => $tour->id,
    'customer_id' => $customer->id,
    'tour_date' => now()->addDays(10), // Future date
    'status' => 'pending',
    'payment_status' => 'pending',
    'total_amount' => 500,
    'customer_type' => 'local', // Assuming required
    'adults_count' => 1,
    'total_participants' => 1,
    'base_price' => 500,
    'discount_amount' => 0,
    'tax_amount' => 0,
]);

echo "Booking ID: {$booking->id} Status: {$booking->status}\n";

echo "Updating Payment to 'paid'...\n";
$booking->update(['payment_status' => 'paid']);

// Reload to check observer effects
$booking->refresh();
echo "New Status: {$booking->status}\n"; // Should be 'confirmed'
echo "Assigned Guides: " . $booking->tourGuides()->count() . "\n";
if ($booking->tourGuides()->count() > 0) {
    echo "Guide Name: " . $booking->tourGuides->first()->full_name . "\n";
} else {
    echo "NO GUIDE ASSIGNED (Check availability logic)\n";
}

// Test Vehicle Assignment (Admin Update)
echo "\nTesting Admin Update (Vehicle Assignment)...\n";
if (Vehicle::count() === 0) {
    Vehicle::create(['registration_number' => 'TEST-001', 'status' => 'available', 'is_available' => 1]);
}
$vehicle = Vehicle::first();

// Improve: Directly call the relationship update as Controller would
$booking->vehicles()->sync([$vehicle->id => [
     'start_date' => $booking->tour_date,
     'end_date' => \Carbon\Carbon::parse($booking->tour_date)->addDays(3),
     'status' => 'reserved'
]]);

$booking->refresh();
echo "Assigned Vehicles: " . $booking->vehicles()->count() . "\n";

echo "\nVerification Complete.\n";
