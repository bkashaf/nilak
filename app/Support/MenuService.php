<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Page;
use App\Models\Setting;

class MenuService
{
    public function fullMenu(): array
{
    $current = url()->current();
    $menu = [];

    // خانه
    $menu[] = [
        'label' => __('messages.home'),
        'url'   => route('home'),
        'type'  => 'static',
        'active' => rtrim($current, '/') === rtrim(route('home'), '/')
    ];

    // فروشگاه
    $menu[] = [
        'label' => __('messages.shop'),
        'url'   => route('shop.index'),
        'type'  => 'static',
        'active' => rtrim($current, '/') === rtrim(route('shop.index'), '/')
    ];

    // دسته‌بندی محصولات
    $root = $this->productCategoryRoot();
    if ($root) {
        $menu[] = [
            'label' => $root->localized_name,
            'url'   => route('shop.index', ['category' => $root->slug]),
            'type'  => 'category',
            'children' => $root->children,
            'active' => str_contains($current, $root->slug)
        ];
    }

    // پیگیری سفارش
    $menu[] = [
        'label' => __('messages.track_order'),
        'url'   => route('order.tracking'),
        'type'  => 'static',
        'active' => rtrim($current, '/') === rtrim(route('order.tracking'), '/')
    ];

    // صفحات داینامیک
    $pages = Page::query()
        ->published()
        ->where('show_in_menu', true)
        ->orderBy('menu_order')
        ->orderBy('id')
        ->get(['title', 'slug']);

    foreach ($pages as $page) {
        $url = route('pages.show', $page->slug);

        $menu[] = [
            'label' => $page->title,
            'url'   => $url,
            'type'  => 'page',
            'active' => rtrim($current, '/') === rtrim($url, '/')
        ];
    }

    return $menu;
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
