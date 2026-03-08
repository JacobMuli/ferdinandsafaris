<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TourGuideAssignment extends Pivot
{
    protected $table = 'booking_tour_guide';

    public $incrementing = true; // If the pivot table has an ID, otherwise false. Assuming it might not, but let's check migration if possible. For now, defaulting to Pivot behavior usually implies no ID unless specified.

    // Actually, HasMany requires an ID or unique key behavior.
    // If it's a true Pivot, belongsToMany uses it.
    // However, TourGuide model has 'return $this->hasMany(TourGuideAssignment::class);'
    // This implies the table has 'tour_guide_id'.
    // If table is 'booking_tour_guide', it has 'tour_guide_id'.

    protected $fillable = [
        'booking_id',
        'tour_guide_id',
        'status',          // pending, accepted, declined
        'is_lead_guide',
        'offered_payment',
        'notes',
    ];

    protected $casts = [
        'is_lead_guide' => 'boolean',
        'offered_payment' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function tourGuide()
    {
        return $this->belongsTo(TourGuide::class);
    }
}
