<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'tour_id',
        'vehicle_type_id', // Foreign key
        'accommodation_id', // Foreign key
        'customer_id',
        'customer_type',
        'tour_date',
        'adults_count',
        'children_count',
        'total_participants',
        'base_price',
        'discount_amount',
        'discount_type',
        'tax_amount',
        'total_amount',
        'actual_price',
        'pricing_breakdown', // JSON
        'status',
        'payment_status',
        'special_requests',
        'participant_details',
        'emergency_contact_name',
        'emergency_contact_phone',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'tour_date' => 'date',
        'participant_details' => 'array',
        'pricing_breakdown' => 'array',
        'base_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = config('safaris.reference_prefix', 'FS-') . strtoupper(Str::random(10));
            }
        });
    }

    // Relationships
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tourGuides()
    {
        return $this->belongsToMany(TourGuide::class, 'booking_tour_guide')
            ->withPivot('is_lead_guide')
            ->withTimestamps();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('tour_date', '>=', now()->toDateString())
                     ->whereIn('status', ['confirmed', 'paid']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
                     ->orWhere(function ($q) {
                         $q->where('tour_date', '<', now()->toDateString())
                           ->whereIn('status', ['confirmed', 'paid']);
                     });
    }

    public function scopeByCustomerType($query, $type)
    {
        return $query->where('customer_type', $type);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tour_date', [$startDate, $endDate]);
    }

    // Helper Methods
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    public function markAsPaid()
    {
        $this->update([
            'payment_status' => 'paid',
            'status' => 'paid',
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
        ]);
    }

    public function getTotalPaid()
    {
        return $this->payments()
                    ->where('status', 'completed')
                    ->sum('amount');
    }

    public function getRemainingBalance()
    {
        return $this->total_amount - $this->getTotalPaid();
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed'])
               && $this->tour_date > now()->addDays(7);
    }

    public function canBeReviewed()
    {
        return $this->status === 'completed'
               && $this->tour_date < now()
               && !$this->reviews()->exists();
    }
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'booking_vehicles')
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

    public function assignAvailableGuide()
    {
        // Find first available and active guide
        // Logic:
        // 1. Get all active guides
        // 2. Filter by availability for tour dates
        // 3. Pick first

        $startDate = $this->tour_date;
        $endDate = Carbon::parse($this->tour_date)->addDays($this->tour->duration_days ?? 1); // Assuming tour has duration

        $availableGuide = TourGuide::active()
            ->get()
            ->filter(function ($guide) use ($startDate, $endDate) {
                return $guide->isAvailableForDateRange($startDate, $endDate);
            })
            ->first();

        if ($availableGuide) {
            $this->tourGuides()->attach($availableGuide->id, [
                'is_lead_guide' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Notify Admin/Guide (Placeholder for Notification Logic)
            // Notification::send($availableGuide, new GuideAssigned($this));

            return $availableGuide;
        }

        return null; // No guide available
    }
    public function recalculateTotals()
    {
        // 1. Base Tour Price
        $basePrice = $this->base_price;

        // 2. Vehicle Costs
        $vehicleCost = $this->vehicles()->get()->sum(function($vehicle) {
            return $vehicle->pivot->total_cost;
        });

        // 3. Park Fees
        $parkFees = $this->tour->parkLocations->reduce(function ($carry, $park) {
            $adultTotal = ($park->entry_fee_adult ?? 0) * $this->adults_count;
            $childTotal = ($park->entry_fee_child ?? 0) * ($this->children_count ?? 0);
            return $carry + $adultTotal + $childTotal;
        }, 0);

        // 4. Tax
        $subtotal = $basePrice + $vehicleCost + $parkFees;
        $discount = $this->discount_amount ?? 0;

        $taxable = $subtotal - $discount;
        $taxAmount = $taxable * config('safaris.tax_rate', 0.16);

        $total = $taxable + $taxAmount;

        $this->updateQuietly([
            'tax_amount' => $taxAmount,
            'total_amount' => $total
        ]);

        return $total;
    }
}