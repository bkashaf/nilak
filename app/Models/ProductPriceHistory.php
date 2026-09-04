<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'changed_by',
        'old_price',
        'new_price',
        'old_compare_price',
        'new_compare_price',
        'change_type',
        'reason',
        'meta',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'old_compare_price' => 'decimal:2',
        'new_compare_price' => 'decimal:2',
        'meta' => 'array',
    ];

    protected $appends = [
        'price_direction',
        'discount_percent',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getPriceDirectionAttribute(): string
    {
        $oldPrice = $this->old_price !== null ? (float) $this->old_price : null;
        $newPrice = $this->new_price !== null ? (float) $this->new_price : null;

        if ($oldPrice === null || $newPrice === null) {
            return 'unknown';
        }

        if ($newPrice > $oldPrice) {
            return 'increase';
        }

        if ($newPrice < $oldPrice) {
            return 'decrease';
        }

        return 'unchanged';
    }

    public function getDiscountPercentAttribute(): ?int
    {
        $comparePrice = $this->new_compare_price !== null ? (float) $this->new_compare_price : null;
        $price = $this->new_price !== null ? (float) $this->new_price : null;

        if ($comparePrice === null || $price === null) {
            return null;
        }

        if ($comparePrice <= 0 || $comparePrice <= $price) {
            return null;
        }

        return (int) round((($comparePrice - $price) / $comparePrice) * 100);
    }
}