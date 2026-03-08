@extends('emails.layout')

@section('content')
    <h2>❌ Booking Cancellation Notice</h2>

    <p>Dear {{ $booking->customer->first_name }},</p>

    <p>We regret to inform you that your booking with Ferdinand Safaris has been cancelled.</p>

    <div class="warning-box" style="background-color: #fee2e2; border-left-color: #ef4444;">
        <p style="color: #991b1b; margin: 0;">
            <strong>Status:</strong> CANCELLED
        </p>
    </div>

    <div class="info-box">
        <h3>📋 Cancelled Booking Details</h3>

        <div class="info-row">
            <span class="info-label">Booking Reference:</span>
            <span class="info-value"><strong>{{ $booking->booking_reference }}</strong></span>
        </div>

        <div class="info-row">
            <span class="info-label">Tour:</span>
            <span class="info-value">{{ $booking->tour->name }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Original Date:</span>
            <span class="info-value">{{ $booking->tour_date->format('F j, Y') }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Total Amount:</span>
            <span class="info-value">${{ number_format($booking->total_amount, 2) }}</span>
        </div>

        @if($booking->cancellation_reason)
        <div class="info-row">
            <span class="info-label">Cancellation Reason:</span>
            <span class="info-value">{{ $booking->cancellation_reason }}</span>
        </div>
        @endif

        <div class="info-row">
            <span class="info-label">Cancelled On:</span>
            <span class="info-value">{{ $booking->cancelled_at ? $booking->cancelled_at->format('F j, Y \a\t g:i A') : now()->format('F j, Y \a\t g:i A') }}</span>
        </div>
    </div>

    <div class="info-box" style="background-color: #eff6ff; border-left-color: #3b82f6;">
        <h3 style="color: #1e40af;">💰 Refund Information</h3>
        <p style="color: #1e3a8a; margin-bottom: 12px;">
            If you have made a payment, our team will process your refund according to our cancellation policy.
        </p>
        <ul style="color: #1e3a8a; margin-left: 20px; margin-bottom: 0;">
            <li>Refund processing time: 5-10 business days</li>
            <li>Refund will be issued to the original payment method</li>
            <li>You'll receive a separate confirmation email once processed</li>
        </ul>
    </div>

    <div class="divider"></div>

    <p><strong>We'd love to have you on another adventure!</strong></p>
    <p>Browse our available tours and find your next safari experience:</p>

    <a href="{{ url('/tours') }}" class="button">
        Explore Other Tours
    </a>

    <div class="divider"></div>

    <p><strong>Questions about this cancellation?</strong></p>
    <p style="font-size: 14px; color: #6b7280;">
        Please contact us at <a href="mailto:{{ config('mail.from.address') }}" style="color: #059669;">{{ config('mail.from.address') }}</a>
        with your booking reference <strong>{{ $booking->booking_reference }}</strong>
    </p>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280;">
        We hope to see you on a future adventure with Ferdinand Safaris! 🌍
    </p>
@endsection
