<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tour;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateTours extends Command
{
    protected $signature = 'tours:cleanup-duplicates {--dry-run : Run without deleting}';
    protected $description = 'Find and remove duplicate tours, keeping the one with bookings or the latest one.';

    public function handle()
    {
        $csvPath = base_path('database/data/tours.csv');
        $this->info("Loading valid tours from CSV: $csvPath");

        if (!file_exists($csvPath)) {
            $this->error("CSV file not found.");
            return;
        }

        // 1. Load CSV Source of Truth
        $handle = fopen($csvPath, 'r');
        $headers = fgetcsv($handle); // skip headers
        $headerMap = array_map(function($h) { return \Illuminate\Support\Str::slug(trim($h), '_'); }, $headers);

        $validNames = [];
        $validSlugs = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < count($headers)) continue;
            $row = array_combine($headerMap, array_slice($data, 0, count($headers)));

            $name = $row['tour_name'] ?? $row['name'] ?? '';
            if ($name) {
                // Store both original name and slug for loose matching
                // Note: The import logic now checks Name and Slug.
                $validNames[] = strtolower(trim($name));
                $validSlugs[] = \Illuminate\Support\Str::slug($name);
            }
        }
        fclose($handle);

        $this->info("Found " . count($validNames) . " valid tours in CSV.");

        // 2. Identify Orphans in DB
        $allTours = Tour::withCount('bookings')->get();
        $deletedCount = 0;
        $skippedCount = 0;
        $keptCount = 0;

        foreach ($allTours as $tour) {
            $isExactSlug = in_array($tour->slug, $validSlugs);
            $isExactName = in_array(strtolower(trim($tour->name)), $validNames);

            if ($isExactSlug || $isExactName) {
                $keptCount++;
                continue; // Matches CSV, keep it.
            }

            // It's an orphan
            if ($tour->bookings_count > 0) {
                 $this->warn("  [SKIP] Orphan ID {$tour->id} ('{$tour->name}') has {$tour->bookings_count} bookings. Keeping for safety.");
                 $skippedCount++;
            } else {
                if (!$this->option('dry-run')) {
                    $tour->forceDelete();
                    $this->line("  [DELETE] Removed Orphan ID {$tour->id} ('{$tour->name}')");
                    $deletedCount++;
                } else {
                    $this->line("  [DRY RUN] Would delete Orphan ID {$tour->id} ('{$tour->name}')");
                }
            }
        }

        $this->info("Cleanup Complete.");
        $this->info("Kept: $keptCount (Matches CSV)");
        $this->info("Deleted: $deletedCount (Orphans without bookings)");
        $this->info("Skipped: $skippedCount (Orphans with bookings)");
    }
}
