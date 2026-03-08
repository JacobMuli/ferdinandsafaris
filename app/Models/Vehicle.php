<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_type_id',
        'registration_number',
        'year',
        'color',

        // Operational / per-unit
        'mileage',
        'last_service_date',
        'next_service_due',
        'maintenance_notes',

        'insurance_company',
        'insurance_policy_number',
        'insurance_expiry_date',
        'road_tax_expiry_date',
        'inspection_expiry_date',

        'status',
        'is_available',
        'available_from',
        'available_to',

        'current_booking_id',
        'assigned_driver_id',

        'current_location',
        'current_latitude',
        'current_longitude',
        'home_base',
        'special_notes',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'last_service_date' => 'date',
        'next_service_due' => 'date',
        'insurance_expiry_date' => 'date',
        'road_tax_expiry_date' => 'date',
        'inspection_expiry_date' => 'date',
        'available_from' => 'date',
        'available_to' => 'date',
        'current_latitude' => 'decimal:7',
        'current_longitude' => 'decimal:7',
    ];

    // Relationships
    public function type()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function currentBooking()
    {
        return $this->belongsTo(Booking::class, 'current_booking_id');
    }

    public function assignedDriver()
    {
        return $this->belongsTo(TourGuide::class, 'assigned_driver_id');
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_vehicles')
            ->withPivot(
                'start_date',
                'end_date',
                'days',
                'driver_id',
                'daily_rate',
                'total_cost',
                'status'
            )
            ->withTimestamps();
    }

    public function tourGuides()
    {
        return $this->belongsToMany(TourGuide::class, 'tour_guide_vehicle')
            ->withPivot('is_primary')
            ->withTimestamps();
    }


    // Scopes
    public function scopeAvailable($query)
    {
        return $query
            ->where('status', 'available')
            ->where('is_available', true);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->type->name} - {$this->registration_number}";
    }

    public function getAgeAttribute()
    {
        return $this->year ? now()->year - $this->year : null;
    }

    public function getNeedsServiceAttribute()
    {
        return $this->next_service_due && $this->next_service_due->isPast();
    }

    public function getHasExpiredDocumentsAttribute()
    {
        return collect([
            $this->insurance_expiry_date,
            $this->road_tax_expiry_date,
            $this->inspection_expiry_date,
        ])->filter(fn ($date) => $date && $date->isPast())->isNotEmpty();
    }

    // Booking logic
    public function assignToBooking($booking, $startDate, $endDate, $driverId = null)
    {
        $days = now()->parse($startDate)->diffInDays($endDate) + 1;
        $rate = $this->type->base_daily_rate;
        $totalCost = $rate * $days;

        return $this->bookings()->attach($booking->id, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days' => $days,
            'driver_id' => $driverId,
            'daily_rate' => $rate,
            'total_cost' => $totalCost,
            'status' => 'reserved',
        ]);
    }

    public function markInUse($bookingId)
    {
        $this->update([
            'status' => 'in_use',
            'is_available' => false,
            'current_booking_id' => $bookingId,
        ]);
    }

    public function markAvailable()
    {
        $this->update([
            'status' => 'available',
            'is_available' => true,
            'current_booking_id' => null,
            'assigned_driver_id' => null,
        ]);
    }

    public function sendForMaintenance($notes = null)
    {
        $this->update([
            'status' => 'maintenance',
            'is_available' => false,
            'maintenance_notes' => $notes,
        ]);
    }
}
