<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * نمایش صفحه اصلی فروشگاه (لیست محصولات)
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $perPage = 12;
        if ($request->has('per_page')) {
            $perPage = (int) $request->input('per_page', $perPage);
            $perPage = $perPage > 0 ? $perPage : 12;
        }

        // -------------------------------
        // 🔥 فیلتر دسته‌بندی (جدید)
        // -------------------------------
        $categorySlug = $request->get('category');
        $attributeFilters = array_filter((array) $request->input('attributes', []));

        if ($categorySlug) {

            // پیدا کردن دسته
            $category = Category::where('slug', $categorySlug)->first();

            if ($category) {

                // گرفتن ID دسته و زیر‌دسته‌ها
                $categoryIds = $this->categoryTreeIds($category);

                // محصولات دسته
                $products = Product::where('is_active', true)
                    ->whereIn('category_id', $categoryIds)
                    ->when($attributeFilters, fn ($query) => $this->applyAttributeFilters($query, $attributeFilters))
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);

                $pageTitle = 'محصولات دسته ' . $category->localized_name;

            } else {

                // اگر دسته پیدا نشد
                $products = Product::where('is_active', true)
                    ->when($attributeFilters, fn ($query) => $this->applyAttributeFilters($query, $attributeFilters))
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);

                $pageTitle = 'فروشگاه نیلَک';
            }

        } else {

            // -------------------------------
            // ❌ کد قبلی (حذف شده)
            // $products = Product::where('is_active', true)
            //     ->orderBy('created_at', 'desc')
            //     ->paginate($perPage);
            // -------------------------------

            // ✔ نسخهٔ جدید بدون دسته انتخاب‌شده
            $products = Product::where('is_active', true)
                ->when($attributeFilters, fn ($query) => $this->applyAttributeFilters($query, $attributeFilters))
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $pageTitle = 'فروشگاه نیلَک';
        }

        $filterableAttributes = Attribute::query()
            ->where('is_filterable', true)
            ->with(['values' => fn ($query) => $query->orderBy('position')])
            ->orderBy('position')
            ->get();

        $products->load(['translations', 'category.translations']);
        $products->appends($request->query());

        // نمایش ویو
        if (view()->exists('themes.default.shop')) {
            return view('themes.default.shop', compact('products', 'pageTitle', 'filterableAttributes'));
        }

        if (view()->exists('themes.shop')) {
            return view('themes.shop', compact('products', 'pageTitle', 'filterableAttributes'));
        }

        return view('themes.default.shop', compact('products', 'pageTitle', 'filterableAttributes'));
    }

    private function categoryTreeIds(Category $category): array
    {
        $ids = [$category->id];

        foreach ($category->children()->get() as $child) {
            $ids = array_merge($ids, $this->categoryTreeIds($child));
        }

        return $ids;
    }

    private function applyAttributeFilters($query, array $filters): void
    {
        foreach ($filters as $attributeSlug => $attributeValueId) {
            $query->whereHas('attributeValues', function ($relation) use ($attributeSlug, $attributeValueId) {
                $relation->whereHas('attribute', fn ($attribute) => $attribute->where('slug', $attributeSlug))
                    ->where('attribute_value_id', (int) $attributeValueId);
            });
        }
    }

    /**
     * نمایش صفحه محصول تکی
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['images', 'category'])
            ->first();

        if (! $product) {
            abort(404);
        }

        if (view()->exists('themes.default.product')) {
            return view('themes.default.product', compact('product'));
        }

        if (view()->exists('themes.product')) {
            return view('themes.product', compact('product'));
        }

        // fallback
        return response()->view('
            <html><body><h1>' . e($product->name) . '</h1><p>نمایش محصول</p></body></html>
        ');
    }

    /**
     * متد قدیمی product
     */
    public function product(string $slug)
    {
        return $this->show($slug);
    }

    /**
     * جستجوی محصولات
     */
    public function search(Request $request)
    {
        $query = (string) $request->input('q', '');
        $perPage = 12;

        $products = Product::where('is_active', true)
            ->when($query !== '', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(['q' => $query]);

        if (view()->exists('themes.default.shop')) {
            return view('themes.default.shop', compact('products'))->with('searchQuery', $query);
        }

        if (view()->exists('themes.shop')) {
            return view('themes.shop', compact('products'))->with('searchQuery', $query);
        }

        return view('themes.default.shop', compact('products'))->with('searchQuery', $query);
    }
}
