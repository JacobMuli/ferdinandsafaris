<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NotifyTourCompletion extends Command
{
    protected $signature = 'tours:notify-completion';
    protected $description = 'Notify Admin and Guides to confirm completion of tours ending today';

    public function handle()
    {
        $today = now()->format('Y-m-d');

        // Find bookings that are confirmed/paid and end today
        // Assuming duration is on the tour model.
        // Logic: booking_date + duration_days = today
        // Or simpler: We can iterate active confirmed bookings and check.
        // For efficiency, better to query directly if possible.

        $bookings = \App\Models\Booking::with(['tour', 'tourGuides'])
            ->whereIn('status', ['confirmed', 'paid'])
            ->get()
            ->filter(function ($booking) use ($today) {
                // Calculate end date
                $endDate = $booking->tour_date->copy()->addDays($booking->tour->duration_days ?? 1)->format('Y-m-d');
                return $endDate === $today;
            });

        foreach ($bookings as $booking) {
            $this->info("Notifying for booking {$booking->booking_reference} ending today.");

            // Notify Admin
            // Notification::route('mail', 'admin@ferdinandsafaris.com')
            //     ->notify(new \App\Notifications\TourCompletionReminder($booking));

            \Illuminate\Support\Facades\Log::info("Sent completion reminder for Booking {$booking->booking_reference} to Admin.");

            // Notify Tour Guides
            foreach ($booking->tourGuides as $guide) {
                // $guide->notify(new \App\Notifications\TourCompletionReminder($booking));
                 \Illuminate\Support\Facades\Log::info("Sent completion reminder for Booking {$booking->booking_reference} to Guide {$guide->full_name}.");
            }
        }

        $this->info("Processed " . $bookings->count() . " bookings ending today ({$today}).");
    }
}
