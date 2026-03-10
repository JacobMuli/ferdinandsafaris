<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Initiate Stripe Payment session.
     */
    public function payment($bookingReference)
    {
        $booking = Booking::where('booking_reference', $bookingReference)
            ->with(['tour', 'customer'])
            ->firstOrFail();

        // Security: Ensure booking is confirmed and has a price set by admin
        if (!in_array($booking->status, ['confirmed']) || !$booking->actual_price) {
            return redirect()->route('bookings.show', $bookingReference)
                ->with('error', 'This booking is not yet ready for payment. Please wait for admin confirmation.');
        }

        try {
            $session = $this->stripeService->createCheckoutSession($booking);
            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe Integration Error: ' . $e->getMessage());
            return back()->with('error', 'Error initiating payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment return from Stripe.
     */
    public function paymentSuccess(Request $request, $bookingReference)
    {
        $booking = Booking::where('booking_reference', $bookingReference)->firstOrFail();
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('bookings.show', $bookingReference)
                ->with('error', 'Payment verification failed: Missing session ID.');
        }

        try {
            $session = $this->stripeService->retrieveSession($sessionId);
            
            if ($session->payment_status !== 'paid') {
                return redirect()->route('bookings.show', $bookingReference)
                    ->with('error', 'Payment has not been completed yet.');
            }

            // Verify this session matches the booking
            if ($session->client_reference_id !== $booking->booking_reference) {
                return redirect()->route('bookings.show', $bookingReference)
                    ->with('error', 'Invalid payment session.');
            }
        } catch (\Exception $e) {
            Log::error('Stripe Verification Error: ' . $e->getMessage());
            return redirect()->route('bookings.show', $bookingReference)
                ->with('error', 'Payment verification failed.');
        }
        
        DB::beginTransaction();
        try {
            if ($booking->payment_status !== 'paid') {
                $booking->markAsPaid();
                $booking->customer->updateBookingStats();
                
                // Create payment record
                Payment::create([
                    'booking_id' => $booking->id,
                    'transaction_id' => 'STRIPE-' . strtoupper(Str::random(12)),
                    'payment_method' => 'stripe',
                    'amount' => $booking->actual_price,
                    'currency' => config('safaris.currency', 'USD'),
                    'status' => 'completed',
                ]);

                // Notify Customer
                try {
                    Mail::to($booking->customer->email)->send(new \App\Mail\BookingConfirmed($booking));
                } catch (\Exception $e) {
                    Log::error('Mail Error: ' . $e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('bookings.show', $bookingReference)
                ->with('success', 'Payment successful! Your adventure is confirmed.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment success processing failed: ' . $e->getMessage());
            return redirect()->route('bookings.show', $bookingReference)
                ->with('error', 'Error updating payment status.');
        }
    }

    /**
     * Handle payment cancellation.
     */
    public function paymentCancel($bookingReference)
    {
        return redirect()->route('bookings.show', $bookingReference)
            ->with('info', 'Payment was cancelled. You can try again when you are ready.');
    }
}
