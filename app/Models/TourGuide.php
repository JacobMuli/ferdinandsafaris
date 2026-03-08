<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class TourGuide extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'user_id',
        'employment_status',
        'first_name',
        'last_name',
        'email',
        'phone',
        'license_number',
        'license_expiry_date',
        'bio',
        'profile_photo',
        'languages',
        'specializations',
        'certifications',
        'rating',
        'total_tours',
        'years_experience',
        'is_available',
        'is_active',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'languages' => 'array',
        'specializations' => 'array',
        'certifications' => 'array',
        'rating' => 'decimal:2',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'license_expiry_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationships
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_tour_guide')
            ->withPivot('is_lead_guide')
            ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(TourGuideAssignment::class);
    }

    public function pendingAssignments()
    {
        return $this->assignments()->where('status', 'pending');
    }

    public function acceptedAssignments()
    {
        return $this->assignments()->whereIn('status', ['accepted', 'confirmed']);
    }

    public function availability()
    {
        return $this->hasMany(TourGuideAvailability::class);
    }

    public function notificationPreferences()
    {
        return $this->hasOne(TourGuideNotificationPreference::class);
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'tour_guide_vehicle')
            ->withPivot('is_primary')
            ->withTimestamps();
    }


    // Accessors
    public function getProfilePhotoUrlAttribute()
    {
        if (!$this->profile_photo) {
            return 'https://ui-avatars.com/api/?name='.urlencode($this->first_name.' '.$this->last_name);
        }

        $image = trim($this->profile_photo);
        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        return \Illuminate\Support\Facades\Storage::url($image);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Methods
    public function isLicenseValid()
    {
        return $this->license_expiry_date->isFuture();
    }

    public function isAvailableForDate($date)
    {
        if (!$this->is_available || !$this->is_active) {
            return false;
        }

        // Check if date falls within any unavailability period
        return !$this->availability()
            ->where('unavailable_from', '<=', $date)
            ->where('unavailable_to', '>=', $date)
            ->exists();
    }

    public function isAvailableForDateRange($startDate, $endDate)
    {
        if (!$this->is_available || !$this->is_active) {
            return false;
        }

        // Check if range overlaps with any unavailability period
        return !$this->availability()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('unavailable_from', [$startDate, $endDate])
                    ->orWhereBetween('unavailable_to', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('unavailable_from', '<=', $startDate)
                          ->where('unavailable_to', '>=', $endDate);
                    });
            })
            ->exists();
    }

    public function markUnavailable($from, $to, $reason = 'other', $notes = null, $bookingId = null)
    {
        return $this->availability()->create([
            'unavailable_from' => $from,
            'unavailable_to' => $to,
            'reason' => $reason,
            'notes' => $notes,
            'booking_id' => $bookingId,
        ]);
    }

    public function updateStats()
    {
        $this->total_tours = $this->bookings()
            ->whereIn('status', ['completed', 'paid'])
            ->count();

        $this->save();
    }

    public function updateRating()
    {
        // Calculate average rating from bookings with reviews
        $avgRating = $this->bookings()
            ->whereHas('review')
            ->with('review')
            ->get()
            ->avg(function ($booking) {
                return $booking->review->rating ?? 0;
            });

        $this->rating = round($avgRating, 2);
        $this->save();
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithSpecialization($query, $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }
}