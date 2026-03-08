<?php

namespace App\Observers;

use App\Models\Booking;
use Filament\Notifications\Notification; // Assuming Filament or standard Laravel notification

class BookingObserver
{
    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Check if payment_status changed to 'paid'
        if ($booking->isDirty('payment_status') && $booking->payment_status === 'paid') {

            // Auto-confirm if not already confirmed/completed
            if ($booking->status === 'pending') {
                $booking->status = 'confirmed';
                $booking->confirmed_at = now();
                $booking->saveQuietly(); // Prevent infinite loop if we listen to status changes too
            }

            // Auto-assign Guide if none assigned
            if ($booking->tourGuides()->count() === 0) {
                $booking->assignAvailableGuide();
            }
        }
    }
}
