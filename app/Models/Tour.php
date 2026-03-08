<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'highlights',
        'itinerary',
        'duration_days',
        'difficulty_level',
        'price_per_person',
        'child_price',
        'group_discount_percentage',
        'min_group_size',
        'corporate_discount_percentage',
        'max_participants',
        'category',
        'location',
        'included_items',
        'excluded_items',
        'requirements',
        'featured_image',
        'gallery_images',
        'is_featured',
        'is_active',
        'available_from',
        'available_to',
        'views',
        'rating',
        'reviews_count',
    ];

    protected $casts = [
        'included_items' => 'array',
        'excluded_items' => 'array',
        'requirements' => 'array',
        'gallery_images' => 'array',
        'highlights' => 'array',
        'itinerary' => 'array',

        'is_featured' => 'boolean',
        'is_active' => 'boolean',

        'available_from' => 'date',
        'available_to' => 'date',

        'price_per_person' => 'decimal:2',
        'child_price' => 'decimal:2',
        'group_discount_percentage' => 'decimal:2',
        'corporate_discount_percentage' => 'decimal:2',

        'rating' => 'decimal:2',
        'views' => 'integer',
        'reviews_count' => 'integer',
        'max_participants' => 'integer',
        'min_group_size' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tour) {
            if (empty($tour->slug)) {
                $tour->slug = Str::slug($tour->name);
            }
        });
    }

    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeAvailableOn($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereNull('available_from')
            ->orWhere('available_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('available_to')
            ->orWhere('available_to', '>=', $date);
        });
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('available_from')
                           ->orWhere('available_from', '<=', now());
                     })
                     ->where(function ($q) {
                         $q->whereNull('available_to')
                           ->orWhere('available_to', '>=', now());
                     });
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helper Methods
    public function isAvailableForDate($date)
    {
        $date = \Carbon\Carbon::parse($date);

        if (!$this->is_active) {
            return false;
        }

        if ($this->available_from && $date->lt($this->available_from)) {
            return false;
        }

        if ($this->available_to && $date->gt($this->available_to)) {
            return false;
        }

        return true;
    }

    public function calculatePrice($customerType, $adultsCount, $childrenCount = 0, $options = [])
    {
        // 1. Base Tour Cost (Service/Guide/margin)
        $basePrice = $adultsCount * $this->price_per_person;
        if ($childrenCount > 0 && $this->child_price) {
            $basePrice += $childrenCount * $this->child_price;
        }

        $breakdown = [
            'base_tour_cost' => $basePrice,
            'accommodation_cost' => 0,
            'vehicle_cost' => 0,
            'park_fees' => 0,
            'discount' => 0,
        ];

        $durationDays = $this->duration_days;

        // 2. Accommodation Cost
        if (!empty($options['accommodation_id'])) {
            $accommodation = $this->accommodations()->find($options['accommodation_id']);
            if (!$accommodation) {
                 // Fallback to finding directly if not in pivot yet (unlikely if strictly filtered, but safe)
                 $accommodation = \App\Models\Accommodation::find($options['accommodation_id']);
            }

            if ($accommodation) {
                // Determine price per night (Pivot override or Model standard)
                // Assuming Standard Season price for now
                $pppn = $accommodation->pivot->price_per_night ?? $accommodation->price_per_night_standard;

                // Logic: Per Person Per Night * Nights * Participants
                // "Per Person Sharing" usually implies the standard rate.
                // Single supplement would be extra, but let's stick to standard calculation first.
                $nights = $accommodation->pivot->nights ?? ($durationDays - 1); // Fallback to duration-1
                if ($nights < 1) $nights = 1;

                $accomCost = ($pppn * $nights * ($adultsCount + $childrenCount));
                // Note: Real world might have child rates for accommodation.

                $breakdown['accommodation_cost'] = $accomCost;
                $basePrice += $accomCost;
            }
        }

        // 3. Vehicle Cost
        if (!empty($options['vehicle_type_id'])) {
            $vehicleType = \App\Models\VehicleType::find($options['vehicle_type_id']);
            if ($vehicleType) {
                // Vehicle price is per day, split by group OR added to total?
                // Usually Vehicle is a fixed cost divided by group, OR added to the quote.
                // Since this returns TOTAL price for the booking:
                $vehicleCost = $vehicleType->base_daily_rate * $durationDays;

                $breakdown['vehicle_cost'] = $vehicleCost;
                $basePrice += $vehicleCost;
            }
        }

        // 4. Park Fees
        $residentStatus = $options['resident_status'] ?? 'non_resident'; // resident, non_resident, citizen
        // Sum up fees for all park locations linked to tour
        $parkFees = 0;
        foreach ($this->parkLocations as $park) {
            // Check if resident fees apply (Note: current DB might only have one fee column, need to check ParkLocation model)
            // ParkLocation model has `entry_fee_adult` and `entry_fee_child`.
            // Often these are Non-Resident by default. Resident fees might differ.
            // For now, use the standard fields.
            $daysInPark = $park->pivot->days ?? 1; // Default to 1 day per park if not specified

            $adultFee = $park->entry_fee_adult * $daysInPark * $adultsCount;
            $childFee = $park->entry_fee_child * $daysInPark * $childrenCount;

            $parkFees += ($adultFee + $childFee);
        }
        $breakdown['park_fees'] = $parkFees;
        $basePrice += $parkFees;


        // 5. Discounts
        $totalParticipants = $adultsCount + $childrenCount;
        $discount = 0;

        if ($customerType === 'group' && $totalParticipants >= $this->min_group_size) {
            $discount = $basePrice * ($this->group_discount_percentage / 100);
        } elseif ($customerType === 'corporate') {
            $discount = $basePrice * ($this->corporate_discount_percentage / 100);
        }

        $breakdown['discount'] = $discount;

        return [
            'base_price' => $basePrice, // This is actually Total before discount now
            'discount' => $discount,
            'final_price' => $basePrice - $discount,
            'breakdown' => $breakdown
        ];
    }

    public function getAvailableSpots($date)
    {
        $bookedSpots = $this->bookings()
            ->where('tour_date', $date)
            ->whereIn('status', ['confirmed', 'paid'])
            ->sum('total_participants');

        return $this->max_participants - $bookedSpots;
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function updateRating()
    {
        $stats = $this->approvedReviews()
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        $this->update([
            'rating' => $stats->avg_rating ?? 0,
            'reviews_count' => $stats->total,
        ]);
    }

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'tour_accommodations')
            ->withPivot('night_number', 'nights', 'room_type', 'board_basis', 'price_per_night', 'included_in_tour_price', 'notes', 'display_order')
            ->withTimestamps();
    }

    public function parkLocations()
    {
        return $this->belongsToMany(ParkLocation::class, 'park_location_tour')
            ->withPivot('day_number', 'duration_hours', 'days', 'is_primary_location')
            ->withTimestamps();
    }

    public function getDisplayImageAttribute()
    {
        // 1. Tour's own featured image
        if ($this->featured_image) {
             $image = trim($this->featured_image);
             if (Str::startsWith($image, ['http://', 'https://'])) {
                 return $image;
             }
             return \Illuminate\Support\Facades\Storage::url($image);
        }

        // 2. Fallback to primary/first Park Location image
        // Ensure parkLocations are loaded or fetch first
        $location = $this->parkLocations->first();
        if ($location && $location->featured_image_url) {
            return $location->featured_image_url;
        }

        // 3. Last resort placeholder
        return 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800';
    }

    public function getGalleryImagesAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (!is_array($value)) {
            return [];
        }

        // Clean whitespace from all URLs in the gallery
        return array_map('trim', $value);
    }

    public function getFormattedItineraryAttribute()
    {
        // If it's already an array, return it (sorted by day if keys are numeric/day numbers)
        if (is_array($this->itinerary)) {
            // Ensure it's sorted by day if the keys are day numbers, or just return as is
            return $this->itinerary;
        }

        // If it's a string (legacy data), try to format it as a single day or raw text
        if (is_string($this->itinerary) && !empty($this->itinerary)) {
            return [
                ['day' => 1, 'title' => 'Tour Itinerary', 'description' => $this->itinerary]
            ];
        }

        return [];
    }


    public function getRouteKeyName()
    {
        return 'slug';
    }
}