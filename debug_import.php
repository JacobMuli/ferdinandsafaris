<?php

use App\Services\TourImportService;
use Illuminate\Support\Str;

$service = new TourImportService();

// Mock Row from CSV Line 96
// "9-Day Red Elephants",9,"Tsavo East","Reserve",1900,950,"https://unsplash.com/s/photos/tsavo","Wilderness giants loop.","Galana River; Yatta Plateau","4x4; Lodge; FB Meals; Entry","Tips; Drinks","Camera","Tsavo East arrival and plateau game tracking.","Galana river bank red elephant intensive search.","Aruba dam morning drive and predator hunt.","Mudanda rock hike and panoramic view session.","Lugard falls river game session tracking day.","Voi river sector intensive red elephant search.","Northern plains drive and remote game search.","Sunrise dawn drive across the dusty savanna.","Final sunrise tracking and return journey depart.","","","",""
// Headers: ID,Tour_Name,Duration_Days,Location,Category,Adult_Price_USD,Child_Price_USD,Featured_Image_URL,Description,Highlights,Included_Items,Excluded_Items,Requirements,Day_1,Day_2,Day_3,Day_4,Day_5,Day_6,Day_7,Day_8,Day_9,Day_10,Day_11,Day_12,Day_13,Day_14

$row = [
    'tour_name' => '9-Day Red Elephants',
    'duration_days' => 9,
    'location' => 'Tsavo East',
    'category' => 'Reserve',
    'adult_price_usd' => 1900,
    'child_price_usd' => 950,
    'featured_image_url' => 'https://unsplash.com/s/photos/tsavo',
    'description' => 'Wilderness giants loop.',
    'highlights' => 'Galana River; Yatta Plateau',
    'included_items' => '4x4; Lodge; FB Meals; Entry',
    'excluded_items' => 'Tips; Drinks',
    'requirements' => 'Camera',
    'day_1' => 'Tsavo East arrival and plateau game tracking.',
    'day_2' => 'Galana river bank red elephant intensive search.',
    // ... simulate sparse array as seen in ImportService
    'itinerary_day_1' => 'Tsavo East arrival and plateau game tracking.', // Try variants
    'day_1' => 'Tsavo East arrival and plateau game tracking.',
];

// Reflection to access protected method
$reflection = new ReflectionClass($service);
$method = $reflection->getMethod('processRow');
$method->setAccessible(true);

try {
    echo "Attempting import...\n";
    $method->invoke($service, $row);
    echo "Import execution finished (Check DB for ID/slug).\n";
    
    $tour = \App\Models\Tour::where('name', '9-Day Red Elephants')->first();
    if ($tour) {
        echo "SUCCESS: Tour found in DB. ID: " . $tour->id . "\n";
        echo "Itinerary Count: " . count($tour->itinerary) . "\n";
        print_r($tour->itinerary);
    } else {
        echo "FAILURE: Tour not found in DB after execution.\n";
    }
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
