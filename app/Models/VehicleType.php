<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',          // renamed from type
        'manufacturer',
        'model',
        'default_capacity',
        'features',
        'base_daily_rate',
        'base_cost_per_km',
        'description',
        'main_image',
        'gallery_images',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'gallery_images' => 'array',
        'base_daily_rate' => 'decimal:2',
        'base_cost_per_km' => 'decimal:2',
        'default_capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    // Accessors
    public function getMainImageUrlAttribute()
    {
        if (!$this->main_image) {
            return 'https://images.unsplash.com/photo-1533473359331-0135ef1bcfb0?w=500'; // Default vehicle
        }

        $image = trim($this->main_image);
        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
             return $image;
        }

        return \Illuminate\Support\Facades\Storage::url($image);
    }

    public function getDisplayNameAttribute()
    {
        return "{$this->manufacturer} {$this->model} ({$this->default_capacity}-Seat)";
    }
}
