@extends('emails.layout')

@section('content')
    <h2>🎉 Booking Request Received!</h2>

    <p>Dear {{ $booking->customer->first_name }},</p>

    <p>Great news! Your booking request with Ferdinand Safaris has been successfully received and is now being reviewed by our team.</p>

    <div class="info-box">
        <h3>📋 Booking Summary</h3>

        <div class="info-row">
            <span class="info-label">Booking Reference:</span>
            <span class="info-value"><strong>{{ $booking->booking_reference }}</strong></span>
        </div>

        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">
                <span style="color: #ca8a04; font-weight: 600;">⌛ PENDING REVIEW</span>
            </span>
        </div>

        <div class="info-row">
            <span class="info-label">Tour:</span>
            <span class="info-value">{{ $booking->tour->name }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Tour Date:</span>
            <span class="info-value">{{ $booking->tour_date->format('F j, Y') }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Duration:</span>
            <span class="info-value">{{ $booking->tour->duration_days }} Days</span>
        </div>

        <div class="info-row">
            <span class="info-label">Participants:</span>
            <span class="info-value">
                {{ $booking->adults_count }} Adult(s)
                @if($booking->children_count > 0)
                    + {{ $booking->children_count }} Child(ren)
                @endif
            </span>
        </div>
    </div>

    <div class="info-box">
        <h3>💰 Payment Summary</h3>

        <div class="info-row">
            <span class="info-label">Total Amount:</span>
            <span class="info-value"><strong>${{ number_format($booking->actual_price ?? $booking->total_amount, 2) }}</strong></span>
        </div>

        <div class="info-row">
            <span class="info-label">Payment Status:</span>
            <span class="info-value">{{ ucfirst($booking->payment_status) }}</span>
        </div>
    </div>

    @if($booking->special_requests)
    <div class="info-box">
        <h3>📝 Special Requests</h3>
        <p style="color: #333;">{{ $booking->special_requests }}</p>
    </div>
    @endif

    <a href="{{ route('bookings.show', $booking->booking_reference) }}" class="button">
        View Booking Details
    </a>

    <div class="divider"></div>

    <p><strong>What's Next?</strong></p>
    <ul style="color: #555; margin-left: 20px; margin-bottom: 20px;">
        <li>Our team will review your booking and confirm availability</li>
        <li>You'll receive a confirmation email once approved</li>
        <li>Complete payment to secure your spot</li>
        <li>We'll send you detailed tour information closer to the date</li>
    </ul>

    <div class="warning-box">
        <p><strong>Important:</strong> Please keep your booking reference <strong>{{ $booking->booking_reference }}</strong> for future correspondence.</p>
    </div>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        Questions? Contact us at <a href="mailto:{{ config('mail.from.address') }}" style="color: #059669;">{{ config('mail.from.address') }}</a>
    </p>
@endsection
