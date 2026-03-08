@extends('emails.layout')

@section('content')
    <h2>Thank You for Contacting Us! 🌍</h2>

    <p>Dear {{ $contactData['name'] }},</p>

    <p>Thank you for reaching out to Ferdinand Safaris! We have received your message and our team will review it shortly.</p>

    <div class="info-box">
        <h3>Your Message Summary</h3>

        <div class="info-row">
            <span class="info-label">Subject:</span>
            <span class="info-value">{{ $contactData['subject'] }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Submitted:</span>
            <span class="info-value">{{ now()->format('F j, Y \a\t g:i A') }}</span>
        </div>
    </div>

    <p><strong>What happens next?</strong></p>
    <ul style="color: #555; margin-left: 20px; margin-bottom: 20px;">
        <li>Our team will review your inquiry within 24 hours</li>
        <li>We'll respond to <strong>{{ $contactData['email'] }}</strong></li>
        <li>For urgent matters, call us directly</li>
    </ul>

    <div class="divider"></div>

    <p><strong>In the meantime, explore our safari packages:</strong></p>

    <a href="{{ url('/tours') }}" class="button">Browse Tours</a>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        If you didn't submit this form, please ignore this email or contact us at
        <a href="mailto:{{ config('mail.from.address') }}" style="color: #059669;">{{ config('mail.from.address') }}</a>
    </p>
@endsection
