<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'cms_page_id',
        'section_key',
        'title',
        'content',
        'order',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function page()
    {
        return $this->belongsTo(CmsPage::class, 'cms_page_id');
    }
}
