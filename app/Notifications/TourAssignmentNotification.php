<?php

namespace App\Notifications;

use App\Models\TourGuideAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TourAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $assignment;
    protected $acceptUrl;
    protected $declineUrl;

    public function __construct(TourGuideAssignment $assignment)
    {
        $this->assignment = $assignment;

        // Generate secure signed URLs for accept/decline
        $this->acceptUrl = URL::temporarySignedRoute(
            'guide.assignment.accept',
            now()->addDays(7),
            ['assignment' => $assignment->id]
        );

        $this->declineUrl = URL::temporarySignedRoute(
            'guide.assignment.decline',
            now()->addDays(7),
            ['assignment' => $assignment->id]
        );
    }

    public function via($notifiable)
    {
        $preferences = $notifiable->notificationPreferences;
        $channels = ['database']; // Always store in database

        if (!$preferences) {
            return array_merge($channels, ['mail']); // Default to email
        }

        if ($preferences->shouldNotifyVia('email') && !$preferences->isInQuietHours()) {
            $channels[] = 'mail';
        }

        // Add SMS/WhatsApp if configured
        // if ($preferences->shouldNotifyVia('sms')) {
        //     $channels[] = 'vonage'; // or 'twilio'
        // }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $booking = $this->assignment->booking;
        $tour = $booking->tour;

        return (new MailMessage)
            ->subject('New Tour Assignment: ' . $tour->name)
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('You have been assigned to a new tour.')
            ->line('**Tour:** ' . $tour->name)
            ->line('**Date:** ' . $booking->tour_date->format('F d, Y'))
            ->line('**Duration:** ' . $tour->duration_days . ' days')
            ->line('**Location:** ' . $tour->location)
            ->line('**Participants:** ' . $booking->total_participants . ' people')
            ->line('**Payment Offered:** $' . number_format((float) $this->assignment->offered_payment, 2))
            ->line('')
            ->line('**Tour Details:**')
            ->line($tour->description)
            ->line('')
            ->action('Accept Assignment', $this->acceptUrl)
            ->line('If you are unavailable, please decline:')
            ->action('Decline Assignment', $this->declineUrl)
            ->line('')
            ->line('Please respond within 24 hours.')
            ->line('Thank you for being part of Ferdinand Safaris team!');
    }

    public function toArray($notifiable)
    {
        $booking = $this->assignment->booking;
        $tour = $booking->tour;

        return [
            'type' => 'tour_assignment',
            'assignment_id' => $this->assignment->id,
            'booking_id' => $booking->id,
            'tour_name' => $tour->name,
            'tour_date' => $booking->tour_date->format('Y-m-d'),
            'duration_days' => $tour->duration_days,
            'participants' => $booking->total_participants,
            'offered_payment' => $this->assignment->offered_payment,
            'is_lead_guide' => $this->assignment->is_lead_guide,
            'accept_url' => $this->acceptUrl,
            'decline_url' => $this->declineUrl,
            'message' => "New tour assignment: {$tour->name} on {$booking->tour_date->format('M d, Y')}",
        ];
    }

    // For SMS/WhatsApp (requires additional packages)
    // For SMS/WhatsApp (requires additional packages)
    /*
    public function toVonage($notifiable)
    {
        $booking = $this->assignment->booking;
        $tour = $booking->tour;
        $preferences = $notifiable->notificationPreferences;

        $message = "Ferdinand Safaris: New tour assignment!\n\n";
        $message .= "Tour: {$tour->name}\n";
        $message .= "Date: {$booking->tour_date->format('M d, Y')}\n";
        $message .= "Payment: \${$this->assignment->offered_payment}\n\n";
        $message .= "Accept: {$this->acceptUrl}\n";
        $message .= "Decline: {$this->declineUrl}";

        // Return VonageMessage when package is installed
        // return (new \Illuminate\Notifications\Messages\VonageMessage)->content($message);
    }
    */
}