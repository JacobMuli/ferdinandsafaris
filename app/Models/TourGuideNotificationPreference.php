<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourGuideNotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_guide_id',
        'email_enabled',
        'email',
        'sms_enabled',
        'sms_phone',
        'whatsapp_enabled',
        'whatsapp_phone',
        'push_enabled',
        'device_token',
        'quiet_hours_start',
        'quiet_hours_end',
        'notify_new_assignments',
        'notify_assignment_reminders',
        'notify_tour_updates',
        'notify_payments',
        'notify_reviews',
        'preferred_language',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'whatsapp_enabled' => 'boolean',
        'push_enabled' => 'boolean',
        'notify_new_assignments' => 'boolean',
        'notify_assignment_reminders' => 'boolean',
        'notify_tour_updates' => 'boolean',
        'notify_payments' => 'boolean',
        'notify_reviews' => 'boolean',
        'quiet_hours_start' => 'datetime:H:i',
        'quiet_hours_end' => 'datetime:H:i',
    ];

    // Relationships
    public function tourGuide()
    {
        return $this->belongsTo(TourGuide::class);
    }

    // Methods
    public function shouldNotifyVia($channel)
    {
        return $this->{$channel . '_enabled'} ?? false;
    }

    public function isInQuietHours()
    {
        if (!$this->quiet_hours_start || !$this->quiet_hours_end) {
            return false;
        }

        $now = now()->format('H:i');
        $start = $this->quiet_hours_start->format('H:i');
        $end = $this->quiet_hours_end->format('H:i');

        if ($start < $end) {
            return $now >= $start && $now <= $end;
        } else {
            // Handles case where quiet hours span midnight
            return $now >= $start || $now <= $end;
        }
    }

    public function getNotificationContact($channel)
    {
        switch ($channel) {
            case 'email':
                return $this->email ?? $this->tourGuide->email;
            case 'sms':
                return $this->sms_phone ?? $this->tourGuide->phone;
            case 'whatsapp':
                return $this->whatsapp_phone ?? $this->tourGuide->phone;
            default:
                return null;
        }
    }
}