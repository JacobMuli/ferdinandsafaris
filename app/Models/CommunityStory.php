<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommunityStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tour_id',
        'name',
        'email',
        'title',
        'content',
        'images',
        'is_approved',
        'is_featured',
        'views'
    ];

    protected $casts = [
        'images' => 'array',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
