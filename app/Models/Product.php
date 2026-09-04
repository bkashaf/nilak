<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
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
        'reserved_stock',
        'is_active',
        'is_featured',
        'meta',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'stock' => 'integer',
        'reserved_stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'meta' => 'array',
    ];

    protected $appends = [
        'has_discount',
        'discount_percent',
        'price_change_percent',
        'formatted_compare_price',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('position');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id')->where('is_primary', true);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(ProductPriceHistory::class, 'product_id')->latest('id');
    }

    public function latestPriceHistory(): HasOne
    {
        return $this->hasOne(ProductPriceHistory::class, 'product_id')->latestOfMany();
    }

    public function translation(?string $locale = null): ?ProductTranslation
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

    public function getLocalizedShortDescriptionAttribute(): ?string
    {
        return $this->translation()?->short_description ?? $this->short_description;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return $this->translation()?->description ?? $this->description;
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    public function getFormattedComparePriceAttribute(): ?string
    {
        if ($this->compare_price === null) {
            return null;
        }

        return number_format((float) $this->compare_price, 2);
    }

    public function getHasDiscountAttribute(): bool
    {
        if ($this->compare_price === null) {
            return false;
        }

        return (float) $this->compare_price > (float) $this->price;
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->has_discount) {
            return null;
        }

        $comparePrice = (float) $this->compare_price;
        $price = (float) $this->price;

        if ($comparePrice <= 0) {
            return null;
        }

        return (int) round((($comparePrice - $price) / $comparePrice) * 100);
    }

    public function getPriceChangePercentAttribute(): ?int
    {
        if ($this->compare_price === null) {
            return null;
        }

        $comparePrice = (float) $this->compare_price;
        $price = (float) $this->price;

        if ($comparePrice <= 0 || $comparePrice == $price) {
            return 0;
        }

        return (int) round((($price - $comparePrice) / $comparePrice) * 100);
    }

    public function getImageUrlAttribute(): string
    {
        $image = $this->primaryImage ?: $this->images->first();

        if ($image && $image->path && Storage::disk('public')->exists($image->path)) {
            return asset('storage/' . ltrim($image->path, '/'));
        }

        return asset('themes/default/images/no-image.svg');
    }

    public function getGroupedAttributeValuesAttribute(): Collection
    {
        $rows = $this->relationLoaded('attributeValues')
            ? $this->attributeValues
            : $this->attributeValues()->with(['attribute', 'attributeValue'])->get();

        return $rows
            ->filter(fn ($row) => $row->attribute && $row->attributeValue)
            ->groupBy('attribute_id')
            ->map(function (Collection $group) {
                return (object) [
                    'attribute' => $group->first()->attribute,
                    'values' => $group
                        ->map(fn ($item) => $item->attributeValue)
                        ->unique('id')
                        ->values(),
                ];
            })
            ->values();
    }

    public function getColorOptionsAttribute(): Collection
    {
        return $this->grouped_attribute_values
            ->first(fn ($group) => in_array($group->attribute->slug, ['color', 'colour'], true))
            ?->values ?? collect();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDiscounted($query)
    {
        return $query->whereNotNull('compare_price')
            ->whereColumn('compare_price', '>', 'price');
    }
}