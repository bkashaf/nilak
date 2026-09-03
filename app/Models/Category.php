<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'status',
        'position',
    ];

    protected $casts = [
        'status' => 'integer',
        'position' => 'integer',
    ];

    /**
     * والد (درختی)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * فرزندان (درختی)
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    /**
     * محصولات مرتبط
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function translation(?string $locale = null): ?CategoryTranslation
    {
        $locale ??= app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);

        if ($translation?->is_published) {
            return $translation;
        }

        return $this->translations->firstWhere('locale', config('app.fallback_locale', 'fa'));
    }

    public function getLocalizedNameAttribute(): string
    {
        return $this->translation()?->name ?? $this->name;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return $this->translation()?->description ?? $this->description;
    }

    /**
     * URL تصویر دسته‌بندی با fallback برای مواردی که تصویر آپلود نشده
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . ltrim($this->image, '/'));
        }

        return asset('themes/default/images/no-image.svg');
    }

    /**
     * Scope برای دسته‌های فعال
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
