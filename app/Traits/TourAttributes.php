<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait TourAttributes
{
    public function getDisplayImageAttribute()
    {
        // 1. Tour's own featured image
        if ($this->featured_image) {
             $image = trim($this->featured_image);
             if (Str::startsWith($image, ['http://', 'https://'])) {
                 return $image;
             }
             return Storage::url($image);
        }

        // 2. Fallback to primary/first Park Location image
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
        if (is_array($this->itinerary)) {
            return $this->itinerary;
        }

        if (is_string($this->itinerary) && !empty($this->itinerary)) {
            return [
                ['day' => 1, 'title' => 'Tour Itinerary', 'description' => $this->itinerary]
            ];
        }

        return [];
    }
}
