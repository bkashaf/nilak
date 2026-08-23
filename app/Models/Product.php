<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'compare_price',
        'stock',
        'is_active',
        'is_featured',
        'meta',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'meta' => 'array',
    ];

    /**
     * دسته محصول
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * تصاویر محصول
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('position');
    }

    /**
     * تصویر اصلی محصول (به‌صورت رابطه)
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id')->where('is_primary', true);
    }

    /**
     * مقادیر ویژگی‌های محصول
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id');
    }

    /**
     * دسترسی سریع به قیمت فرمت‌شده
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2);
    }

    /**
     * URL تصویر قابل نمایش محصول با fallback برای فایل‌های حذف‌شده یا ناموجود
     */
    public function getImageUrlAttribute(): string
    {
        $image = $this->primaryImage ?: $this->images->first();

        if ($image && $image->path && Storage::disk('public')->exists($image->path)) {
            return asset('storage/' . ltrim($image->path, '/'));
        }

        return asset('themes/default/images/no-image.svg');
    }

    /**
     * Scope برای محصولات فعال
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
