<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Support\SliderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(1, (int) $request->input('per_page', 12));
        $categorySlug = trim((string) $request->input('category', ''));
        $attributeFilters = collect((array) $request->input('attributes', []))
            ->filter(function ($value) {
                if (is_array($value)) {
                    return count(array_filter($value, fn ($item) => $item !== null && $item !== '')) > 0;
                }

                return $value !== null && $value !== '';
            })
            ->all();
        $sort = (string) $request->input('sort', 'newest');

        $priceMinInput = $request->input('price_min');
        $priceMaxInput = $request->input('price_max');

        $priceMin = $priceMinInput === null || $priceMinInput === '' ? null : (int) $priceMinInput;
        $priceMax = $priceMaxInput === null || $priceMaxInput === '' ? null : (int) $priceMaxInput;

        $query = Product::query()
            ->active()
            ->with(['translations', 'category.translations', 'primaryImage']);

        if ($categorySlug !== '') {
            $category = Category::where('slug', $categorySlug)->first();

            if ($category) {
                $query->whereIn('category_id', $this->categoryTreeIds($category));
                $pageTitle = 'محصولات دسته ' . $category->localized_name;
            } else {
                $pageTitle = 'فروشگاه نیلَک';
            }
        } else {
            $pageTitle = 'فروشگاه نیلَک';
        }

        $this->applyAttributeFilters($query, $attributeFilters);

        if ($priceMin !== null) {
            $query->where('price', '>=', $priceMin);
        }

        if ($priceMax !== null) {
            $query->where('price', '<=', $priceMax);
        }

        $this->applySort($query, $sort);

        $products = $query->paginate($perPage)->appends($request->query());

        return $this->renderShopView($products, $pageTitle, $request);
    }

    private function renderShopView($products, string $pageTitle, Request $request)
    {
        $filterableAttributes = Attribute::query()
            ->where('is_filterable', true)
            ->with(['values' => fn ($query) => $query->orderBy('position')])
            ->orderBy('position')
            ->get();

        $priceBounds = [
            'min' => (int) (Product::active()->min('price') ?? 0),
            'max' => (int) (Product::active()->max('price') ?? 0),
        ];

        $priceBounds['max'] = max($priceBounds['max'], $priceBounds['min']);

        $shopSlider = app(SliderService::class)->byKey('shop_hero', 3);

        $viewData = compact(
            'products',
            'pageTitle',
            'filterableAttributes',
            'priceBounds',
            'shopSlider'
        );

        if ($request->filled('q')) {
            $viewData['searchQuery'] = (string) $request->input('q');
        }

        if (view()->exists('themes.default.shop')) {
            return view('themes.default.shop', $viewData);
        }

        if (view()->exists('themes.shop')) {
            return view('themes.shop', $viewData);
        }

        return view('themes.default.shop', $viewData);
    }

    private function categoryTreeIds(Category $category): array
    {
        $ids = [$category->id];

        foreach ($category->children()->get() as $child) {
            $ids = array_merge($ids, $this->categoryTreeIds($child));
        }

        return $ids;
    }

    private function applyAttributeFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $attributeSlug => $attributeValueIds) {
            $attributeValueIds = array_values(array_filter(array_map('intval', (array) $attributeValueIds)));

            if (empty($attributeValueIds)) {
                continue;
            }

            $query->whereHas('attributeValues', function ($relation) use ($attributeSlug, $attributeValueIds) {
                $relation->whereHas('attribute', fn ($attribute) => $attribute->where('slug', $attributeSlug))
                    ->whereIn('attribute_value_id', $attributeValueIds);
            });
        }
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'discount' => $query->orderByRaw(
                'CASE WHEN compare_price > price THEN (compare_price - price) ELSE 0 END DESC'
            ),
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    public function show(string $slug)
    {
        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->with([
                'images',
                'category',
                'translations',
                'primaryImage',
                'attributeValues.attribute',
                'attributeValues.attributeValue',
            ])
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

        return response()->view(
            '<html><body><h1>' . e($product->name) . '</h1><p>نمایش محصول</p></body></html>'
        );
    }

    public function product(string $slug)
    {
        return $this->show($slug);
    }

    public function search(Request $request)
    {
        $queryText = trim((string) $request->input('q', ''));
        $perPage = 12;

        $products = Product::query()
            ->active()
            ->with(['translations', 'category.translations', 'primaryImage'])
            ->when($queryText !== '', function (Builder $query) use ($queryText) {
                $query->where(function (Builder $innerQuery) use ($queryText) {
                    $innerQuery->where('name', 'like', "%{$queryText}%")
                        ->orWhere('description', 'like', "%{$queryText}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(['q' => $queryText]);

        return $this->renderShopView($products, 'نتایج جستجو', $request);
    }
}