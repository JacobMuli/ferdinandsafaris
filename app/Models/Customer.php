<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'customer_type',
        'company_name',
        'tax_id',
        'special_requirements',
        'newsletter_subscribed',
        'total_bookings',
        'total_spent',
        'preferred_contact_method',
        'emergency_contact_name',
        'emergency_contact_phone',
        'avatar',
    ];

    protected $casts = [
        'newsletter_subscribed' => 'boolean',
        'total_spent' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('customer_type', $type);
    }

    public function scopeNewsletterSubscribers($query)
    {
        return $query->where('newsletter_subscribed', true);
    }

    // Helper Methods
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
             return 'https://ui-avatars.com/api/?name='.urlencode($this->first_name.' '.$this->last_name);
        }

        $image = trim($this->avatar);
        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        return \Illuminate\Support\Facades\Storage::url($image);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function updateBookingStats()
    {
        $stats = $this->bookings()
            ->whereIn('status', ['paid', 'completed'])
            ->selectRaw('COUNT(*) as count, SUM(total_amount) as total')
            ->first();

        $this->update([
            'total_bookings' => $stats->count ?? 0,
            'total_spent' => $stats->total ?? 0,
        ]);
    }

    public function isVip()
    {
        return $this->total_bookings >= 5 || $this->total_spent >= 5000;
    }

    public function getDiscountEligibility()
    {
        if ($this->total_bookings >= 10) {
            return 15; // 15% loyalty discount
        } elseif ($this->total_bookings >= 5) {
            return 10; // 10% loyalty discount
        } elseif ($this->total_bookings >= 3) {
            return 5; // 5% loyalty discount
        }
        return 0;
    }
}