<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Booking;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Booking $booking)
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => config('safaris.currency', 'usd'),
                    'product_data' => [
                        'name' => 'Safari Booking: ' . $booking->tour->name,
                        'description' => 'Reference: ' . $booking->booking_reference,
                    ],
                    'unit_amount' => $booking->actual_price * 100, // Stripe uses cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('bookings.payment.success', $booking->booking_reference) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('bookings.payment.cancel', $booking->booking_reference),
            'customer_email' => $booking->customer->email,
            'client_reference_id' => $booking->booking_reference,
            'metadata' => [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
            ],
        ]);
    }

    public function retrieveSession($sessionId)
    {
        return Session::retrieve($sessionId);
    }
}
