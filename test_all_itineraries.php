<?php

use App\Models\Tour;

echo "Testing Itinerary Display for All Tours\n";
echo str_repeat('=', 80) . "\n\n";

$tours = Tour::orderBy('duration_days')->orderBy('name')->get();

$totalTours = $tours->count();
$toursWithItinerary = 0;
$toursWithoutItinerary = 0;
$mismatchedDuration = [];

foreach ($tours as $tour) {
    $itineraryCount = is_array($tour->itinerary) ? count($tour->itinerary) : 0;
    
    if ($itineraryCount > 0) {
        $toursWithItinerary++;
        
        // Check if itinerary count matches duration
        if ($itineraryCount != $tour->duration_days) {
            $mismatchedDuration[] = [
                'id' => $tour->id,
                'name' => $tour->name,
                'duration' => $tour->duration_days,
                'itinerary_count' => $itineraryCount
            ];
        }
    } else {
        $toursWithoutItinerary++;
        echo "❌ NO ITINERARY: {$tour->id} | {$tour->name} ({$tour->duration_days} days)\n";
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "SUMMARY:\n";
echo "Total Tours: $totalTours\n";
echo "Tours WITH Itinerary: $toursWithItinerary\n";
echo "Tours WITHOUT Itinerary: $toursWithoutItinerary\n";

if (!empty($mismatchedDuration)) {
    echo "\nTours with Duration/Itinerary Count Mismatch:\n";
    echo str_repeat('-', 80) . "\n";
    foreach ($mismatchedDuration as $info) {
        echo sprintf("%-5d %-50s Duration: %2d | Itinerary: %2d\n", 
            $info['id'], 
            substr($info['name'], 0, 47),
            $info['duration'],
            $info['itinerary_count']
        );
    }
}

echo "\n" . str_repeat('=', 80) . "\n";

if ($toursWithItinerary == $totalTours) {
    echo "✅ SUCCESS: All tours have itineraries!\n";
} else {
    echo "⚠️  WARNING: {$toursWithoutItinerary} tours are missing itineraries\n";
}
