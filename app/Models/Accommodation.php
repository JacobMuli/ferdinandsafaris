<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Accommodation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'category',
        'description',
        'amenities',
        'room_types',
        'address',
        'city',
        'region',
        'country',
        'latitude',
        'longitude',
        'park_location_id',
        'phone',
        'email',

        'total_rooms',
        'max_guests',
        'price_per_night_budget',
        'price_per_night_standard',
        'price_per_night_deluxe',
        'price_per_night_suite',
        'breakfast_included',
        'lunch_included',
        'dinner_included',
        'board_basis',
        'rating',
        'review_count',
        'main_image',
        'gallery_images',
        'family_friendly',
        'pet_friendly',
        'wheelchair_accessible',
        'airport_transfer_available',
        'is_available',
        'requires_deposit',
        'min_nights',
        'max_nights',
        'cancellation_policy',
        'house_rules',
        'check_in_time',
        'check_out_time',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'amenities' => 'array',
        'room_types' => 'array',
        'gallery_images' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',

        'price_per_night_budget' => 'decimal:2',
        'price_per_night_standard' => 'decimal:2',
        'price_per_night_deluxe' => 'decimal:2',
        'price_per_night_suite' => 'decimal:2',

        'rating' => 'decimal:1',

        'breakfast_included' => 'boolean',
        'lunch_included' => 'boolean',
        'dinner_included' => 'boolean',
        'family_friendly' => 'boolean',
        'pet_friendly' => 'boolean',
        'wheelchair_accessible' => 'boolean',
        'airport_transfer_available' => 'boolean',
        'is_available' => 'boolean',
        'requires_deposit' => 'boolean',

        'check_in_time' => 'string',
        'check_out_time' => 'string',
    ];

    // Relationships
    public function parkLocation()
    {
        return $this->belongsTo(ParkLocation::class);
    }

    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'tour_accommodations')
            ->withPivot('night_number', 'nights', 'room_type', 'board_basis', 'price_per_night', 'included_in_tour_price', 'notes', 'display_order')
            ->withTimestamps();
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_accommodations')
            ->withPivot('check_in_date', 'check_out_date', 'nights', 'room_type', 'number_of_rooms', 'guests_per_room', 'board_basis', 'price_per_night', 'total_price', 'confirmation_number', 'status', 'special_requests', 'guest_preferences', 'internal_notes')
            ->withTimestamps();
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeNearPark($query, $parkLocationId)
    {
        return $query->where('park_location_id', $parkLocationId);
    }

    public function scopeRatedAbove($query, $rating)
    {
        return $query->where('rating', '>=', $rating);
    }

    // Accessors
    public function getMainImageUrlAttribute()
    {
        if (!$this->main_image) {
            return 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500';
        }

        $image = trim($this->main_image);
        if (Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        return \Illuminate\Support\Facades\Storage::url($image);
    }

    public function getTypeNameAttribute()
    {
        return Str::title(str_replace('_', ' ', $this->type));
    }

    public function getCategoryNameAttribute()
    {
        return Str::title(str_replace('_', ' ', $this->category));
    }

    public function getLowestPriceAttribute()
    {
        $prices = array_filter([
            $this->price_per_night_budget,
            $this->price_per_night_standard,
            $this->price_per_night_deluxe,
            $this->price_per_night_suite,
        ]);

        return empty($prices) ? null : min($prices);
    }

    public function getHighestPriceAttribute()
    {
        $prices = array_filter([
            $this->price_per_night_budget,
            $this->price_per_night_standard,
            $this->price_per_night_deluxe,
            $this->price_per_night_suite,
        ]);

        return empty($prices) ? null : max($prices);
    }

    public function getPriceRangeAttribute()
    {
        return '$' . number_format($this->lowest_price, 0) . ' - $' . number_format($this->highest_price, 0);
    }

    public function getFullAddressAttribute()
    {
        return trim("{$this->address}, {$this->city}, {$this->country}");
    }

    // Methods
    public function isAvailableForDates($checkIn, $checkOut)
    {
        if (!$this->is_available) {
            return false;
        }

        // Check if there are conflicting bookings
        $conflictingBookings = $this->bookings()
            ->wherePivot('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<=', $checkIn)
                          ->where('check_out_date', '>=', $checkOut);
                    });
            })
            ->sum('booking_accommodations.number_of_rooms');

            // NOTE: Availability is calculated at property level, not per room type
        return $conflictingBookings < $this->total_rooms;
    }

    public function getAvailableRooms($checkIn, $checkOut)
    {
        $bookedRooms = $this->bookings()
            ->wherePivot('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<=', $checkIn)
                          ->where('check_out_date', '>=', $checkOut);
                    });
            })
            ->sum('booking_accommodations.number_of_rooms');

        return max(0, $this->total_rooms - $bookedRooms);
    }

    public function updateRating()
    {
        // This would calculate average rating from reviews
        // Placeholder for now
        return $this;
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($accommodation) {
            if (empty($accommodation->slug)) {
                $accommodation->slug = Str::slug($accommodation->name);
            }
        });

        static::updating(function ($accommodation) {
            if ($accommodation->isDirty('name')) {
                $accommodation->slug = Str::slug($accommodation->name);
            }
        });
    }
}