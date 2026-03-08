<?php

namespace App\Services;

use App\Models\Tour;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TourImportService
{
    public function importCsv($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $handle = fopen($filePath, 'r');

        // Read headers
        $headers = fgetcsv($handle);

        // Map CSV headers to flexible keys (lower case, trimmed)
        $headerMap = array_map(function($h) {
            return Str::slug(trim($h), '_');
        }, $headers);

        $importedCount = 0;
        $errors = [];

        while (($data = fgetcsv($handle)) !== false) {
            // Handle column count mismatches gracefully
            $headerCount = count($headers);
            $dataCount = count($data);

            if ($dataCount < $headerCount) {
                // Pad missing columns with empty strings
                $data = array_pad($data, $headerCount, '');
            } elseif ($dataCount > $headerCount) {
                // Trim extra columns (likely from quoted commas in descriptions)
                $data = array_slice($data, 0, $headerCount);
            }

            $row = array_combine($headerMap, $data);

            try {
                $this->processRow($row);
                $importedCount++;
            } catch (\Exception $e) {
                $errors[] = "Error importing row: " . $e->getMessage();
            }
        }

        fclose($handle);

        return [
            'count' => $importedCount,
            'errors' => $errors
        ];
    }

    protected function processRow($row)
    {
        // 1. Basic Mapping
        $tourData = [
            'name' => $row['tour_name'] ?? $row['name'] ?? 'Untitled Tour',
            'duration_days' => (int)($row['duration_days'] ?? 1),
            'location' => $row['location'] ?? 'Kenya',
            'category' => strtolower($row['category'] ?? 'wildlife'),
            'price_per_person' => (float)($row['adult_price_usd'] ?? $row['price_per_person'] ?? 0),
            'child_price' => (float)($row['child_price_usd'] ?? $row['child_price'] ?? 0),
            'description' => $row['description'] ?? '',
            'featured_image' => $row['featured_image_url'] ?? $row['featured_image'] ?? null,
            'is_active' => true, // Default to active
            'is_featured' => false,
            // Defaults satisfying DB constraints
            'difficulty_level' => 'moderate',
            'min_group_size' => 2,
            'max_participants' => 12,
        ];

        // 2. Slug generation
        $tourData['slug'] = Str::slug($tourData['name']);

        // 3. Array Parsing (Fuzzy Matching)
        $findKey = function($keywords, $exclude = []) use ($row) {
            foreach (array_keys($row) as $key) {
                // Check excludes first
                foreach ($exclude as $ex) {
                    if (str_contains($key, $ex)) continue 2;
                }
                // Check inclusions
                foreach ($keywords as $word) {
                    if (str_contains($key, $word)) return $key;
                }
            }
            return null;
        };

        // Improved keywords
        $dbToCsvMap = [
            'highlights' => $findKey(['highlight']),
            'included_items' => $findKey(['include', 'inclus'], ['exclude', 'not']),
            'excluded_items' => $findKey(['exclu', 'not_include']),
            'requirements' => $findKey(['require', 'essential'])
        ];

        foreach ($dbToCsvMap as $dbKey => $csvKey) {
            if ($csvKey && !empty($row[$csvKey])) {
                $tourData[$dbKey] = $this->parseList($row[$csvKey]);
            } else {
                 $tourData[$dbKey] = []; // Ensure empty array if not found
            }
        }

        // 4. Itinerary Parsing
        // Strategy A: Scan all keys for Day patterns dynamically
        $itinerary = [];
        $dayMap = []; // dayNum => content

        foreach ($row as $key => $value) {
            // Check for "Day X", "Day_X", "Day-X", "Day:X", "Day X" (case insensitive)
            // Relaxed: matches "itinerary_day_1" or "day_1"
            if (preg_match('/day[\s_\-\.:]*0*(\d+)/i', $key, $matches)) {
                 $dayNum = (int)$matches[1];
                 if (!empty($value)) {
                     $dayMap[$dayNum] = trim($value);
                 }
            }
            // Also check for just numeric headers "1", "2", "3" if they are clearly days
            elseif (preg_match('/^0*(\d+)$/', $key, $matches)) {
                 $dayNum = (int)$matches[1];
                 if ($dayNum > 0 && $dayNum <= 30 && !empty($value)) {
                     $dayMap[$dayNum] = trim($value);
                 }
            }
        }

        if (!empty($dayMap)) {
            ksort($dayMap);
            foreach ($dayMap as $num => $desc) {
                $itinerary[] = [
                    'day' => $num,
                    'title' => "Day {$num}",
                    'description' => $desc
                ];
            }
            // Auto-update duration if itinerary is longer
            if (count($itinerary) > $tourData['duration_days']) {
                $tourData['duration_days'] = count($itinerary);
            }
        }

        // Strategy B: Fallback to single text column if no days found
        if (empty($itinerary)) {
            $itineraryKey = $findKey(['itinerary', 'program', 'schedule', 'route', 'trip_plan']);
            if ($itineraryKey && !empty($row[$itineraryKey])) {
                $itinerary = $this->parseItinerary($row[$itineraryKey], $tourData['duration_days']);
            }
        }

        $tourData['itinerary'] = $itinerary;


        // 5. Find Existing Tour (Smart Matching)
        $tour = Tour::where('slug', $tourData['slug'])->first();

        if (!$tour) {
            // Fallback: Try matching by Name (case-insensitive)
            $tour = Tour::whereRaw('LOWER(name) = ?', [strtolower($tourData['name'])])->first();

            // If found by name, ensure we update the slug to match standard if needed,
            // or just use the existing one. For now, we update the record with new data.
        }

        if ($tour) {
             // Update existing
             $tour->fill($tourData);
        } else {
            // Create new
            $tour = new Tour($tourData);
        }

        // 6. Save Tour
        $tour->save();

        // 6. Handle Park Locations (Many-to-Many)
        // Split location string by semicolon, comma, or newline
        $locationString = $row['location'] ?? $row['locations'] ?? 'Kenya';
        $locations = preg_split('/[;,\n\r]+/', $locationString, -1, PREG_SPLIT_NO_EMPTY);
        $locationIds = [];

        foreach ($locations as $locName) {
            $locName = trim($locName);
            if (empty($locName)) continue;

            // Find or Create ParkLocation
            $parkLocation = \App\Models\ParkLocation::firstOrCreate(
                ['name' => $locName],
                [
                    'slug' => Str::slug($locName),
                    'description' => $locName,
                    'type' => 'National Park', // Default to avoid 1364 error
                    'country' => 'Kenya'      // Default
                ]
            );
            $locationIds[] = $parkLocation->id;
        }

        if (!empty($locationIds)) {
            $tour->parkLocations()->sync($locationIds);
        }
    }

    protected function parseList($string)
    {
        if (empty($string)) return [];
        // Split by semicolon OR newline, trim whitespace
        return array_values(array_filter(array_map('trim', preg_split('/[;\n\r]+/', $string))));
    }

    protected function parseItinerary($text, $expectedDays)
    {
        if (empty($text)) return [];

        // Flexible Regex to capture "Day X", "Day X:", "Day X -", "Day X.", OR "1.", "2:", "3-"

        // Pattern to find "Day 1..." or "1...." markers
        // Case 1: "Day 1"
        // Case 2: "1." or "1:" at start of line or preceded by word boundary (e.g. "Desc.2:")
        $pattern = '/(?:^|\b)(?:Day\s*(\d+)|\(?(\d+)[\.:\)\-])/i';

        $days = [];

        // Simpler approach: Match all "Headers" and their offsets, then slice text.
        preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE);

        if (count($matches[0]) > 0) {
            foreach ($matches[0] as $index => $match) {
                $start = $match[1]; // Offset
                $headerStr = $match[0];

                // Which number did we capture? Group 1 or 2?
                $dayNum = !empty($matches[1][$index][0]) ? $matches[1][$index][0] : $matches[2][$index][0];
                $dayNum = (int)$dayNum;

                // End of this section is Start of Next (or End of String)
                $nextStart = isset($matches[0][$index + 1]) ? $matches[0][$index + 1][1] : strlen($text);
                $length = $nextStart - $start - strlen($headerStr);

                $content = substr($text, $start + strlen($headerStr), $length);

                $days[] = [
                    'day' => $dayNum,
                    'title' => "Day {$dayNum}",
                    'description' => trim($content, " ;.,-:\t\n\r")
                ];
            }
        } else {
             // Fallback: No day markers found, return specific single day
             $days[] = [
                'day' => 1,
                'title' => 'Day 1',
                'description' => $text
            ];
        }

        return $days;
    }
}
