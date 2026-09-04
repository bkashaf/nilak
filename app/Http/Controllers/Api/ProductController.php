<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with([
            'category',
            'images',
            'primaryImage',
            'translations',
            'attributeValues.attribute',
            'attributeValues.attributeValue',
        ]);

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        if ($request->filled('category')) {
            $query->where('category_id', (int) $request->input('category'));
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->input('price_max'));
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        if ($request->boolean('discounted')) {
            $query->discounted();
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function (Builder $innerQuery) use ($search) {
                $innerQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $this->applyAttributeFilters($query, (array) $request->input('attributes', []));
        $this->applySort($query, (string) $request->input('sort', 'newest'));

        $perPage = max(1, (int) $request->input('per_page', 15));

        return response()->json($query->paginate($perPage)->appends($request->query()));
    }

    public function show(int $id)
    {
        $product = Product::query()->with([
            'category',
            'images',
            'primaryImage',
            'translations',
            'attributeValues.attribute',
            'attributeValues.attributeValue',
            'latestPriceHistory.changedBy',
        ])->findOrFail($id);

        return response()->json($product);
    }

    private function applyAttributeFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $slug => $value) {
            if (blank($value)) {
                continue;
            }

            $query->whereHas('attributeValues', function (Builder $relationQuery) use ($slug, $value) {
                $relationQuery->where('attribute_value_id', (int) $value)
                    ->whereHas('attribute', fn (Builder $attributeQuery) => $attributeQuery->where('slug', $slug));
            });
        }
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'discount' => $query->orderByRaw(
                'CASE WHEN compare_price > price THEN (compare_price - price) ELSE 0 END DESC'
            ),
            default => $query->orderBy('created_at', 'desc'),
        };
    }
}