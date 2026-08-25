<?php

namespace App\Support;

use App\Models\Slider;

class SliderService
{
    public function byKey(string $key = 'home_hero', int $limit = 3)
    {
        $locale = app()->getLocale();

        return Slider::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->where(function ($query) use ($locale) {
                $query->where('locale', $locale)->orWhereNull('locale');
            })
            ->orderByRaw('CASE WHEN locale = ? THEN 0 ELSE 1 END', [$locale])
            ->orderBy('position')
            ->limit(max(1, min(3, $limit)))
            ->get();
    }
}
