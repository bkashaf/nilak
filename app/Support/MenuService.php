<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Page;
use App\Models\Setting;

class MenuService
{
    public function topLinks(): array
    {
        $links = [
            ['label' => __('messages.home'), 'url' => route('home')],
            ['label' => __('messages.shop'), 'url' => route('shop.index')],
            ['label' => __('messages.cart'), 'url' => route('cart.index')],
            ['label' => __('messages.checkout'), 'url' => route('checkout.index')],
            ['label' => __('messages.track_order'), 'url' => route('orders.track.form')],
        ];

        $pages = Page::query()
            ->published()
            ->where('show_in_menu', true)
            ->orderBy('menu_order')
            ->orderBy('id')
            ->get(['title', 'slug']);

        foreach ($pages as $page) {
            $links[] = [
                'label' => $page->title,
                'url' => route('pages.show', $page->slug),
            ];
        }

        $current = url()->current();

        return collect($links)->map(function (array $link) use ($current) {
            $normalized = rtrim($link['url'], '/');
            $isActive = $normalized !== '' && (rtrim($current, '/') === $normalized);

            return $link + ['active' => $isActive];
        })->all();
    }

    public function productCategoryRoot(): ?Category
    {
        $slug = Setting::get('menu_product_category_slug', 'product-categories');

        return Category::active()
            ->where('slug', $slug)
            ->with(['children' => fn ($query) => $query->active()->with('children')])
            ->first();
    }
}
