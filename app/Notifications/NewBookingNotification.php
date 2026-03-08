<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class NewBookingNotification extends Notification
{
    use Queueable;

    public $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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
                    ->subject('New Booking Received: ' . $this->booking->reference)
                    ->greeting('Hello Admin,')
                    ->line('A new booking has been placed.')
                    ->line('Tour: ' . $this->booking->tour->name)
                    ->line('Customer: ' . $this->booking->customer->first_name . ' ' . $this->booking->customer->last_name)
                    ->line('Amount: $' . number_format((float) $this->booking->total_amount, 2))
                    ->action('View Booking', route('admin.bookings.show', $this->booking->id));
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
            'amount' => $this->booking->total_amount,
            'customer_name' => $this->booking->customer->first_name . ' ' . $this->booking->customer->last_name,
            'message' => 'New booking for ' . $this->booking->tour->name,
        ];
    }
}
