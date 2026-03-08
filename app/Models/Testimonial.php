<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Testimonial extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'country',
        'message',
        'rating',
        'is_featured',
        'is_approved',
        'source',
        'avatar',
    ];
}
