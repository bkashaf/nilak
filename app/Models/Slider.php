<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'key',
        'title',
        'subtitle',
        'image_path',
        'link_url',
        'link_text',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];
}
