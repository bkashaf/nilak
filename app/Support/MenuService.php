<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Setting;

class MenuService
{
    public function topLinks(): array
    {
        return [
            ['label' => __('messages.home'), 'url' => route('home')],
            ['label' => __('messages.shop'), 'url' => route('shop.index')],
            ['label' => __('messages.cart'), 'url' => route('cart.index')],
            ['label' => __('messages.checkout'), 'url' => route('checkout.index')],
            ['label' => __('messages.track_order'), 'url' => route('orders.track.form')],
        ];
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
