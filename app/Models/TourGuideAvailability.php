<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourGuideAvailability extends Model
{
    use HasFactory;

    protected $table = 'tour_guide_availability';

    protected $fillable = [
        'tour_guide_id',
        'unavailable_from',
        'unavailable_to',
        'reason',
        'notes',
        'booking_id',
    ];

    protected $casts = [
        'unavailable_from' => 'date',
        'unavailable_to' => 'date',
    ];

    // Relationships
    public function tourGuide()
    {
        return $this->belongsTo(TourGuide::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class)->nullable();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('unavailable_to', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('unavailable_to', '<', now());
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('unavailable_from', '<=', $date)
            ->where('unavailable_to', '>=', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('unavailable_from', [$startDate, $endDate])
                ->orWhereBetween('unavailable_to', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('unavailable_from', '<=', $startDate)
                       ->where('unavailable_to', '>=', $endDate);
                });
        });
    }
}