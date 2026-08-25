<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'key',
        'locale',
        'title',
        'subtitle',
        'description',
        'image_path',
        'mobile_image_path',
        'link_url',
        'link_text',
        'focal_x',
        'focal_y',
        'mobile_focal_x',
        'mobile_focal_y',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
        'focal_x' => 'integer',
        'focal_y' => 'integer',
        'mobile_focal_x' => 'integer',
        'mobile_focal_y' => 'integer',
    ];
}
