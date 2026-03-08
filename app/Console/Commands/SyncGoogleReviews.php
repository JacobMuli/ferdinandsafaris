<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Log;

class SyncGoogleReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google-reviews:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync reviews from Google Places API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Google Reviews Sync...');

        // Placeholder for API logic
        // In a real scenario, we would use HTTP Client to fetch reviews using GOOGLE_PLACES_API_KEY
        // $apiKey = config('services.google.places_api_key');
        // $placeId = config('services.google.place_id');

        // Mocking the behavior for now as requested
        $mockReviews = [
            [
                'author_name' => 'Google User ' . rand(100, 999),
                'rating' => 5,
                'text' => 'Amazing experience found via Google! #synced',
                'relative_time_description' => 'a week ago'
            ]
        ];

        foreach ($mockReviews as $review) {
            Testimonial::updateOrCreate(
                [
                    'name' => $review['author_name'],
                    'source' => 'google'
                ],
                [
                    'message' => $review['text'],
                    'rating' => $review['rating'],
                    'is_approved' => true, // Auto-approve synced reviews? Or set to false
                    'is_featured' => false,
                    'country' => 'Google',
                ]
            );
        }

        $this->info('Reviews synced successfully.');
        Log::info('Google Reviews Synced.');
    }
}
