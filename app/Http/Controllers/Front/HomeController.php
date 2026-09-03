<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Support\MenuService;
use App\Support\SliderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * تعداد دسته‌بندی‌های ویژه نمایش‌داده‌شده در صفحه اصلی
     */
    private const FEATURED_CATEGORIES_LIMIT = 4;

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

        // دسته‌بندی‌های ویژه: همان زیرمجموعه‌های ریشهٔ منو، با ترتیب مدیریت‌شده توسط ادمین (فیلد position)
        $categoryRoot = app(MenuService::class)->productCategoryRoot();
        $featuredCategories = ($categoryRoot?->children ?? collect())->take(self::FEATURED_CATEGORIES_LIMIT);

        // محصولات جدید (آخرین 8 محصول فعال)
        $newProducts = Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // پرفروش‌ترین‌ها: مجموع تعداد فروخته‌شده از روی سفارش‌های پرداخت‌شده/ارسال‌شده/تحویل‌شده
        $bestSellers = Product::where('is_active', true)
            ->withSum(['orderItems as sold_quantity' => function ($query) {
                $query->whereHas('order', fn ($q) => $q->whereIn('status', ['paid', 'shipped', 'delivered']));
            }], 'quantity')
            ->orderByDesc('sold_quantity')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        // اگر هنوز فروش واقعی ثبت نشده (فروشگاه تازه‌کار)، جدیدترین‌ها را جایگزین کن تا بخش خالی نماند
        if ((int) ($bestSellers->sum('sold_quantity')) === 0) {
            $bestSellers = $newProducts;
        }

        $homeSlider = app(SliderService::class)->byKey('home_hero', 3);

        // نمایش صفحه Home
        return view('themes.default.home', compact('featuredCategories', 'newProducts', 'bestSellers', 'homeSlider'));
    }
}
