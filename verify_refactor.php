<?php

use App\Models\Tour;
use App\Models\Booking;
use App\Models\Customer;
use App\Services\PricingService;
use Carbon\Carbon;

echo "--- Verifying Refactor ---\n";

// 1. Verify Tour Date Availability
echo "\n1. Testing Tour Availability:\n";
$tour = Tour::first();
if (!$tour) {
    die("No tours found.\n");
}
$tour->available_from = now()->addDays(1);
$tour->available_to = now()->addDays(30);
$tour->save();

$validDate = now()->addDays(5);
$invalidDatePast = now()->subDay();
$invalidDateFuture = now()->addDays(40);

echo "Checking valid date (" . $validDate->toDateString() . "): " . ($tour->isAvailableForDate($validDate) ? 'PASS' : 'FAIL') . "\n";
echo "Checking past date (" . $invalidDatePast->toDateString() . "): " . (!$tour->isAvailableForDate($invalidDatePast) ? 'PASS' : 'FAIL') . "\n";
echo "Checking future date (" . $invalidDateFuture->toDateString() . "): " . (!$tour->isAvailableForDate($invalidDateFuture) ? 'PASS' : 'FAIL') . "\n";


// 2. Verify Pricing Service
echo "\n2. Testing PricingService:\n";
$service = new PricingService();

$tour->price_per_person = 100;
$tour->child_price = 50;
$tour->min_group_size = 5;
$tour->group_discount_percentage = 10;
$tour->parkLocations()->detach(); // Clear park fees for deterministic test
$tour->save();

// Scenario A: Individual (1 Adult)
$priceA = $service->calculateTourPrice($tour, 1, 0, 'individual');
// Base: 100, Tax: 16% of 100 = 16, Total: 116
echo "Individual (1 Adult): ";
echo "Expected ~116. Got: " . $priceA['total_amount'] . " ... " . ($priceA['total_amount'] == 116.00 ? 'PASS' : 'FAIL') . "\n";

// Scenario B: Group (5 Adults)
// Base: 500, Discount: 10% (50) -> 450. Tax: 16% of 450 = 72. Total: 522.
$priceB = $service->calculateTourPrice($tour, 5, 0, 'group');
echo "Group (5 Adults): ";
echo "Expected ~522. Got: " . $priceB['total_amount'] . " ... " . ($priceB['total_amount'] == 522.00 ? 'PASS' : 'FAIL') . "\n";

echo "\nVerification Complete.\n";
