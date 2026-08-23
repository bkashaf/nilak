<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
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
     * Scope برای دسته‌های فعال
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
