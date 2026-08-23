<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'images',
            'attributeValues.attribute',
            'attributeValues.attributeValue',
        ]);

        if ($request->has('active')) {
            $query->where('is_active', (bool) $request->boolean('active'));
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (int) $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (int) $request->price_max);
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        if ($request->boolean('discounted')) {
            $query->whereColumn('compare_price', '>', 'price');
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('short_description', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;

                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;

                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;

                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;

                case 'discount':
                    $query->orderByRaw('(compare_price - price) DESC');
                    break;
            }
        }

        if ($request->has('attributes')) {
            foreach ($request->attributes as $slug => $value) {
                $query->whereHas('attributeValues', function ($q) use ($slug, $value) {
                    $q->where('attribute_value_id', (int) $value)
                        ->whereHas('attribute', fn ($attribute) => $attribute->where('slug', $slug));
                });
            }
        }

        $perPage = (int) $request->input('per_page', 15);

        return response()->json($query->paginate($perPage));
    }

    public function show($id)
    {
        $product = Product::with([
            'category',
            'images',
            'attributeValues.attribute',
            'attributeValues.attributeValue',
        ])->findOrFail($id);

        return response()->json($product);
    }
}
