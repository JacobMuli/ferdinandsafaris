@extends('emails.layout')

@section('content')
    <h2>🎊 Your Booking is Confirmed!</h2>

    <p>Dear {{ $booking->customer->first_name }},</p>

    <p>Excellent news! Your safari adventure has been officially confirmed by our team. Get ready for an unforgettable experience!</p>

    <div class="info-box" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left-color: #10b981;">
        <h3 style="color: #047857;">✓ OFFICIALLY CONFIRMED</h3>
        <p style="color: #065f46; margin: 0;">Your booking has been reviewed and approved. Your adventure awaits!</p>
    </div>

    <div class="info-box">
        <h3>🎯 Booking Details</h3>

        <div class="info-row">
            <span class="info-label">Reference:</span>
            <span class="info-value"><strong>{{ $booking->booking_reference }}</strong></span>
        </div>

        <div class="info-row">
            <span class="info-label">Tour:</span>
            <span class="info-value">{{ $booking->tour->name }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Date:</span>
            <span class="info-value">{{ $booking->tour_date->format('l, F j, Y') }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Duration:</span>
            <span class="info-value">{{ $booking->tour->duration_days }} Days / {{ $booking->tour->duration_days - 1 }} Nights</span>
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

        <div class="info-row">
            <span class="info-label">Total Amount:</span>
            <span class="info-value"><strong style="color: #059669; font-size: 18px;">${{ number_format($booking->actual_price ?? $booking->total_amount, 2) }}</strong></span>
        </div>
    </div>

    <div class="info-row" style="margin-top: 20px; text-align: center;">
        @if($booking->payment_status === 'pending')
            <a href="{{ route('bookings.payment', $booking->booking_reference) }}" class="button" style="background-color: #059669;">
                💳 COMPLETE PAYMENT NOW
            </a>
            <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">Pay securely via Stripe</p>
        @endif
    </div>

    <div class="divider"></div>

    <p><strong>📋 Preparation Checklist:</strong></p>
    <ul style="color: #555; margin-left: 20px; margin-bottom: 20px;">
        <li>✓ Booking confirmed - no further action needed</li>
        <li>Complete payment if not already done</li>
        <li>Ensure valid passport (6+ months validity)</li>
        <li>Check visa requirements for your nationality</li>
        <li>Consider travel insurance</li>
        <li>Pack appropriate clothing and gear</li>
    </ul>

    <div class="info-box" style="background-color: #eff6ff; border-left-color: #3b82f6;">
        <h3 style="color: #1e40af;">💡 Pro Tips</h3>
        <ul style="color: #1e3a8a; margin-left: 20px; margin-bottom: 0;">
            <li>Bring binoculars for better wildlife viewing</li>
            <li>Camera with good zoom lens recommended</li>
            <li>Comfortable walking shoes essential</li>
            <li>Sunscreen and hat for sun protection</li>
        </ul>
    </div>

    <div class="divider"></div>

    <p><strong>Need to make changes?</strong></p>
    <p style="font-size: 14px; color: #6b7280;">
        Contact us at <a href="mailto:{{ config('mail.from.address') }}" style="color: #059669;">{{ config('mail.from.address') }}</a>
        with your booking reference <strong>{{ $booking->booking_reference }}</strong>
    </p>

    <div class="divider"></div>

    <p style="text-align: center; font-size: 18px; color: #059669; font-weight: 600;">
        We can't wait to show you the wonders of Africa! 🦁🐘🦒
    </p>
@endsection
