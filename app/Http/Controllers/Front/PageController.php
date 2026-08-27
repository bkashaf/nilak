<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        // یافتن صفحه منتشرشده با اسلاگ + بارگذاری بلوک‌ها
        $page = Page::published()
            ->where('slug', $slug)
            ->with('blocks')   // بارگذاری بلوک‌ها برای صفحه‌ساز
            ->firstOrFail();

        // ارسال صفحه و بلوک‌ها به ویو
        return view('themes.default.pages.show', [
            'page'   => $page,
            'blocks' => $page->blocks,
        ]);
    }
}
