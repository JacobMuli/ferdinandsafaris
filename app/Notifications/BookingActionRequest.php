<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingActionRequest extends Notification
{
    public $booking;
    public $type;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($booking, $type, $reason)
    {
        $this->booking = $booking;
        $this->type = $type;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Booking ' . ucfirst($this->type) . ' Request: ' . $this->booking->booking_reference)
            ->greeting('Hello Admin,')
            ->line('A customer has requested a ' . $this->type . ' for their booking.')
            ->line('**Booking Reference:** ' . $this->booking->booking_reference)
            ->line('**Tour:** ' . $this->booking->tour->name)
            ->line('**Customer:** ' . $this->booking->customer->full_name)
            ->line('**Reason for Request:**')
            ->line($this->reason)
            ->action('View Booking', route('admin.bookings.show', $this->booking))
            ->line('Please review this request and contact the customer.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'type' => $this->type,
            'reason' => $this->reason,
            'message' => 'New ' . $this->type . ' request for ' . $this->booking->booking_reference,
        ];
    }
}
