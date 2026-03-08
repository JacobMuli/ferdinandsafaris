@extends('emails.layout')

@section('content')
    <h2>📧 New Contact Form Submission</h2>

    <p>You have received a new message through the Ferdinand Safaris contact form.</p>

    <div class="info-box">
        <h3>Contact Details</h3>

        <div class="info-row">
            <span class="info-label">Name:</span>
            <span class="info-value">{{ $contactData['name'] }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">
                <a href="mailto:{{ $contactData['email'] }}" style="color: #059669;">{{ $contactData['email'] }}</a>
            </span>
        </div>

        @if(!empty($contactData['phone']))
        <div class="info-row">
            <span class="info-label">Phone:</span>
            <span class="info-value">{{ $contactData['phone'] }}</span>
        </div>
        @endif

        <div class="info-row">
            <span class="info-label">Subject:</span>
            <span class="info-value">{{ $contactData['subject'] }}</span>
        </div>
    </div>

    <div class="info-box">
        <h3>Message</h3>
        <p style="white-space: pre-wrap; color: #333;">{{ $contactData['message'] }}</p>
    </div>

    <a href="mailto:{{ $contactData['email'] }}?subject=Re: {{ $contactData['subject'] }}" class="button">
        Reply to {{ $contactData['name'] }}
    </a>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        <strong>Quick Action:</strong> You can reply directly to this email to respond to {{ $contactData['name'] }}.
    </p>
@endsection
