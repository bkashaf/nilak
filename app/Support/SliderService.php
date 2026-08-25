<?php

namespace App\Support;

use App\Models\Slider;

class SliderService
{
    public function byKey(string $key = 'home_hero', int $limit = 3)
    {
        return Slider::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->orderBy('position')
            ->limit(max(1, min(3, $limit)))
            ->get();
    }
}
