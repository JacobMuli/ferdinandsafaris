<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\TourGuide;
use App\Models\TourGuideAssignment;
use App\Notifications\TourAssignmentNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TourGuideAssignmentService
{
    /**
     * Offer a tour to a guide
     */
    public function offerTourToGuide(
        Booking $booking,
        TourGuide $guide,
        $offeredPayment = null,
        $isLeadGuide = false,
        $specialInstructions = null
    ) {
        // Check if guide is available
        $tourEndDate = $booking->tour_date->copy()->addDays($booking->tour->duration_days - 1);

        if (!$guide->isAvailableForDateRange($booking->tour_date, $tourEndDate)) {
            throw new \Exception("Guide {$guide->full_name} is not available for these dates.");
        }

        // Create assignment
        $assignment = TourGuideAssignment::create([
            'booking_id' => $booking->id,
            'tour_guide_id' => $guide->id,
            'status' => 'pending',
            'is_lead_guide' => $isLeadGuide,
            'offered_payment' => $offeredPayment ?? $this->calculateGuidePayment($booking, $isLeadGuide),
            'special_instructions' => $specialInstructions,
            'offered_at' => now(),
        ]);

        // Send notifications
        $this->sendAssignmentNotifications($assignment, $guide);

        return $assignment;
    }

    /**
     * Offer tour to multiple guides (for bidding/selection)
     */
    public function offerTourToMultipleGuides(
        Booking $booking,
        array $guideIds,
        $offeredPayment = null,
        $specialInstructions = null
    ) {
        $assignments = [];

        foreach ($guideIds as $guideId) {
            try {
                $guide = TourGuide::findOrFail($guideId);
                $assignments[] = $this->offerTourToGuide(
                    $booking,
                    $guide,
                    $offeredPayment,
                    false,
                    $specialInstructions
                );
            } catch (\Exception $e) {
                Log::error("Failed to offer tour to guide {$guideId}: " . $e->getMessage());
            }
        }

        return $assignments;
    }

    /**
     * Auto-assign best available guides
     */
    public function autoAssignGuides(Booking $booking, $numberOfGuides = 1)
    {
        $tourEndDate = $booking->tour_date->copy()->addDays($booking->tour->duration_days - 1);

        // Find available guides with matching specialization
        $availableGuides = TourGuide::active()
            ->where('is_available', true)
            ->whereDoesntHave('availability', function ($query) use ($booking, $tourEndDate) {
                $query->forDateRange($booking->tour_date, $tourEndDate);
            })
            ->orderByDesc('rating')
            ->orderByDesc('total_tours')
            ->take($numberOfGuides)
            ->get();

        if ($availableGuides->count() < $numberOfGuides) {
            throw new \Exception("Not enough available guides for this tour.");
        }

        $assignments = [];
        foreach ($availableGuides as $index => $guide) {
            $assignments[] = $this->offerTourToGuide(
                $booking,
                $guide,
                null,
                $index === 0, // First guide is lead
                null
            );
        }

        return $assignments;
    }

    /**
     * Send all configured notifications for an assignment
     */
    protected function sendAssignmentNotifications(TourGuideAssignment $assignment, TourGuide $guide)
    {
        try {
            // Send notification via Laravel's notification system
            $guide->notify(new TourAssignmentNotification($assignment));

            // Mark email as sent
            $assignment->update([
                'email_sent' => true,
                'email_sent_at' => now(),
            ]);

            // If SMS/WhatsApp is configured, those would be sent via the notification channels
            // The notification channels will check preferences and send accordingly

        } catch (\Exception $e) {
            Log::error("Failed to send assignment notification: " . $e->getMessage());
        }
    }

    /**
     * Calculate suggested payment for guide
     */
    protected function calculateGuidePayment(Booking $booking, $isLeadGuide = false)
    {
        $tour = $booking->tour;

        // Base payment: 15-20% of tour price
        $basePercentage = $isLeadGuide ? 0.20 : 0.15;
        $payment = $booking->total_amount * $basePercentage;

        // Adjust for duration
        $payment = $payment * $tour->duration_days;

        // Minimum payment
        $minimumPayment = $isLeadGuide ? 200 : 150;

        return max($payment, $minimumPayment);
    }

    /**
     * Accept an assignment
     */
    public function acceptAssignment(TourGuideAssignment $assignment, $responseMethod = 'app')
    {
        DB::transaction(function () use ($assignment, $responseMethod) {
            $assignment->accept($responseMethod);

            // If there are other pending assignments for this booking, you might want to:
            // Option 1: Auto-decline them
            // Option 2: Keep them pending for backup

            // For now, let's keep them pending
        });

        return $assignment;
    }

    /**
     * Decline an assignment
     */
    public function declineAssignment(TourGuideAssignment $assignment, $reason = null, $responseMethod = 'app')
    {
        DB::transaction(function () use ($assignment, $reason, $responseMethod) {
            $assignment->decline($reason, $responseMethod);

            // Optionally: Offer to next best available guide automatically
            // $this->offerToNextBestGuide($assignment->booking);
        });

        return $assignment;
    }

    /**
     * Confirm a guide assignment (admin action)
     */
    public function confirmAssignment(TourGuideAssignment $assignment)
    {
        DB::transaction(function () use ($assignment) {
            $assignment->confirm();

            // Decline other pending assignments for this booking
            TourGuideAssignment::where('booking_id', $assignment->booking_id)
                ->where('id', '!=', $assignment->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'cancelled',
                    'decline_reason' => 'Another guide was confirmed'
                ]);
        });

        return $assignment;
    }

    /**
     * Get available guides for a specific date range
     */
    public function getAvailableGuides($startDate, $endDate, $specialization = null)
    {
        $query = TourGuide::active()
            ->where('is_available', true)
            ->whereDoesntHave('availability', function ($q) use ($startDate, $endDate) {
                $q->forDateRange($startDate, $endDate);
            });

        if ($specialization) {
            $query->withSpecialization($specialization);
        }

        return $query->orderByDesc('rating')
            ->orderByDesc('total_tours')
            ->get();
    }
}