<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendReviewRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-review-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookings = \App\Models\Booking::where('status', 'Completed')
            ->where('updated_at', '<=', now()->subHours(48))
            ->get();

        foreach ($bookings as $booking) {
            $this->info("Processing review request for: " . $booking->reference);
            
            \App\Models\ActivityLog::create([
                'user_id' => null,
                'action' => 'automated_review_request',
                'model_type' => 'App\Models\Booking',
                'model_id' => $booking->id,
                'description' => 'Automated review request sent to ' . $booking->customer_email
            ]);
        }

        $this->info($bookings->count() . " requests handled.");
        return 0;
    }
}
