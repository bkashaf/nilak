<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\SliderService;

class HomeController extends Controller
{
    /**
     * نمایش صفحه اصلی (Home)
     */
    public function index()
    {
        // محصولات جدید (آخرین 8 محصول فعال)
        $newProducts = Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // پرفروش‌ترین‌ها (فعلاً بر اساس شناسه یا تاریخ ایجاد)
$bestSellers = Product::where('is_active', true)
    ->orderBy('created_at', 'desc') // ← جایگزین sales_count
    ->take(8)
    ->get();


        $homeSlider = app(SliderService::class)->byKey('home_hero', 3);

        // نمایش صفحه Home
        return view('themes.default.home', compact('newProducts', 'bestSellers', 'homeSlider'));
    }
}
