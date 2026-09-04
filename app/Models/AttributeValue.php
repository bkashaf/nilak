<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
        'color_hex',
        'meta',
        'slug',
        'position',
    ];

    protected $casts = [
        'meta' => 'array',
        'position' => 'integer',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    public function getNormalizedColorHexAttribute(): ?string
    {
        if (! $this->color_hex) {
            return null;
        }

        $value = trim((string) $this->color_hex);

        if ($value === '') {
            return null;
        }

        if ($value[0] !== '#') {
            $value = '#' . $value;
        }

        return strtoupper($value);
    }
}