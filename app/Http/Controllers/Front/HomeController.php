<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Support\SliderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * نمایش صفحه اصلی (Home)
     */
    public function index(): View|RedirectResponse
    {
        $landingTarget = (string) Setting::get('default_landing_target', 'home');

        if ($landingTarget === 'shop') {
            return redirect()->route('shop.index');
        }

        if ($landingTarget === 'page') {
            $pageId = (int) Setting::get('default_landing_page_id', 0);
            if ($pageId > 0) {
                $page = Page::query()
                    ->published()
                    ->find($pageId);

                if ($page) {
                    return view('themes.default.page', compact('page'));
                }
            }
        }

        // محصولات جدید (آخرین 8 محصول فعال)
        $newProducts = Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // پرفروش‌ترین‌ها (فعلا بر اساس تاریخ ایجاد)
        $bestSellers = Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();


        $homeSlider = app(SliderService::class)->byKey('home_hero', 3);

        // نمایش صفحه Home
        return view('themes.default.home', compact('newProducts', 'bestSellers', 'homeSlider'));
    }
}
