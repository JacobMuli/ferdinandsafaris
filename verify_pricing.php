<?php

use App\Models\Tour;
use App\Models\ParkLocation;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Vehicle;

$tour = Tour::first();
if (!$tour) {
    exit("No tours found.\n");
}

// Setup Park
$park = ParkLocation::first();
if ($park) {
    $park->update(['entry_fee_adult' => 50, 'entry_fee_child' => 25]);
    if (!$tour->parkLocations->contains($park->id)) {
        $tour->parkLocations()->attach($park->id, ['day_number' => 1]);
    }
    $tour->load('parkLocations');
    echo "Park linked. Fees: Adult 50, Child 25.\n";
} else {
    echo "No parks found.\n";
}

// Create Booking
// 2 Adults, 1 Child. Base Price for tour = 1000 (manually set for test)
$booking = Booking::create([
    'tour_id' => $tour->id,
    'customer_id' => Customer::first()->id ?? Customer::factory()->create()->id,
    'tour_date' => now()->addDays(10),
    'adults_count' => 2,
    'children_count' => 1,
    'total_participants' => 3,
    'base_price' => 1000, 
    'status' => 'confirmed', 
    'payment_status' => 'pending',
    'customer_type' => 'individual',
    'booking_reference' => 'TEST-PRICE-' . rand(1000,9999),
    'total_amount' => 1000, // Initial placeholder
    // Required fields
    'first_name' => 'Verify',
    'last_name' => 'Pricing',
    'email' => 'verify@pricing.com',
    'phone' => '0000',
    'country' => 'Testland',
    'emergency_contact_name' => 'Mom',
    'emergency_contact_phone' => '111',
]);

echo "Booking Created. ID: {$booking->id}. Base Price: 1000.\n";

// Assign Vehicle
$vehicle = Vehicle::first();
$vehicleCost = 0;
if ($vehicle) {
    $days = 5;
    $rate = 100;
    $vehicleCost = $days * $rate; // 500
    
    $booking->vehicles()->attach($vehicle->id, [
        'start_date' => now(),
        'end_date' => now()->addDays($days),
        'days' => $days,
        'daily_rate' => $rate,
        'total_cost' => $vehicleCost,
        'status' => 'reserved'
    ]);
    echo "Vehicle Assigned. Cost: {$vehicleCost} (5 days * 100).\n";
} else {
    echo "No vehicles found.\n";
}

// Recalculate
$newTotal = $booking->recalculateTotals();

// Expected:
// Base: 1000
// Vehicle: 500
// Park: (2 * 50) + (1 * 25) = 125
// Subtotal: 1625
// Tax (16%): 260
// Total: 1885

echo "\nVerification Results:\n";
echo "Calculated Total: {$newTotal}\n";
echo "Tax Amount: {$booking->tax_amount}\n";
echo "Total Amount in DB: {$booking->total_amount}\n";

$expectedSub = 1000 + $vehicleCost + ($park ? 125 : 0);
$expectedTax = $expectedSub * 0.16;
$expectedTotal = $expectedSub + $expectedTax;

echo "Expected Total: {$expectedTotal}\n";

if (abs($newTotal - $expectedTotal) < 0.1) {
    echo "SUCCESS: Calculation matches expected logic.\n";
} else {
    echo "FAILURE: Calculation mismatch.\n";
}
