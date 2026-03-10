<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'is_active',
        'subscribed_at'
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'is_active' => 'boolean'
    ];
}
