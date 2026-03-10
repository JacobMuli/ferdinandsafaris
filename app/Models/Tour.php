<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use App\Traits\LogsActivity;
use App\Traits\TourAttributes;
use App\Services\TourPricingService;

class Tour extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, TourAttributes;

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

    public function likes()
    {
        return $this->hasMany(TourLike::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
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
        return app(TourPricingService::class)->calculate($this, $customerType, $adultsCount, $childrenCount, $options);
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

    public function getRouteKeyName()
    {
        return 'slug';
    }
}