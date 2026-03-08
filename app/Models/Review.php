<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'booking_id',
        'customer_id',
        'rating',
        'comment',
        'photos',
        'is_verified',
        'is_approved',
    ];

    protected $casts = [
        'photos' => 'array',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function approve()
    {
        $this->update(['is_approved' => true]);
        $this->tour->updateRating();
    }

    public function reject()
    {
        $this->update(['is_approved' => false]);
    }
}