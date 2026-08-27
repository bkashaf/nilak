<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageBlock extends Model
{
    protected $table = 'page_blocks';

    protected $fillable = [
        'page_id',
        'type',
        'data',
        'position',
    ];

    protected $casts = [
        'data' => 'array', // JSON → array
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
