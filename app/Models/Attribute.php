<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'selection_mode',
        'display_mode',
        'config',
        'is_filterable',
        'is_required',
        'position',
    ];

    protected $casts = [
        'config' => 'array',
        'is_filterable' => 'boolean',
        'is_required' => 'boolean',
        'position' => 'integer',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id')
            ->orderBy('position')
            ->orderBy('id');
    }

    public function allowsMultiple(): bool
    {
        return $this->selection_mode === 'multiple';
    }

    public function usesSwatches(): bool
    {
        return $this->display_mode === 'swatch' || in_array($this->slug, ['color', 'colour'], true);
    }
}