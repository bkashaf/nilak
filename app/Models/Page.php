<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
        'show_in_menu',
        'menu_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'show_in_menu' => 'boolean',
        'menu_order' => 'integer',
    ];

    /**
     * فقط صفحات منتشرشده
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * ارتباط با بلوک‌های صفحه‌ساز
     */
    public function blocks()
    {
        return $this->hasMany(PageBlock::class)->orderBy('position');
    }
}
