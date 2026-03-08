<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ParkLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'parks_locations';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'country',
        'region',
        'description',
        'featured_image',
        'gallery_images',
        'latitude',
        'longitude',
        'area_size_km2',
        'elevation_meters',
        'entry_fee_adult',
        'entry_fee_child',
        'best_time_to_visit',
        'opening_hours',
        'wildlife',
        'activities',
        'facilities',
        'highlights',
        'contact_phone',


        'access_info',
        'rules_regulations',
        'is_active',
        'is_featured',
        'popularity_rank',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'opening_hours' => 'array',
        'wildlife' => 'array',
        'activities' => 'array',
        'facilities' => 'array',
        'highlights' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'entry_fee_adult' => 'decimal:2',
        'entry_fee_child' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected $appends = ['featured_image_url'];

    public function getFeaturedImageUrlAttribute()
    {
        if (!$this->featured_image) {
            return 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=500';
        }

        $image = trim($this->featured_image);

        if (Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        return \Illuminate\Support\Facades\Storage::url($image);
    }

    // Relationships
    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'park_location_tour')
            ->withPivot('day_number', 'duration_hours', 'days', 'is_primary_location')
            ->withTimestamps();
    }

    // Accessors
    public function getLocationCoordinatesAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
            ];
        }
        return null;
    }

    // Methods
    public function getTotalEntryFee($adults = 1, $children = 0)
    {
        $adultFee = $this->entry_fee_adult ?? 0;
        $childFee = $this->entry_fee_child ?? 0;

        return ($adults * $adultFee) + ($children * $childFee);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('popularity_rank', 'desc');
    }

    public function scopeNearby($query, $latitude, $longitude, $radius = 50)
    {
        // Simple distance calculation (in km)
        return $query->selectRaw("*,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance", [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->orderBy('distance');
    }
}