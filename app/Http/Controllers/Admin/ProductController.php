<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $products = Product::with('category', 'primaryImage')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('themes.admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $attributes = Attribute::with('values')->orderBy('position')->get();

        return view('themes.admin.products.create', compact('categories', 'attributes'));
    }

    public function store(Request $request)
    {
        $this->normalizeBooleanInputs($request);

        $data = $request->validate([
            'name_fa' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'short_description_fa' => ['nullable', 'string'],
            'short_description_en' => ['nullable', 'string'],
            'description_fa' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'gte:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'meta' => ['nullable', 'json'],
            'attribute_values' => ['nullable', 'array'],
            'attribute_values.*' => ['nullable', 'array'],
            'attribute_values.*.*' => ['nullable', 'integer', 'exists:attribute_values,id'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'price_change_reason' => ['nullable', 'string', 'max:255'],
        ], [
            'compare_price.gte' => 'قیمت قبل از تخفیف باید بزرگ‌تر یا مساوی قیمت فعلی باشد.',
        ]);

        $data = $this->prepareProductData($request, $data);

        DB::beginTransaction();

        try {
            $product = Product::create($data);

            $this->saveTranslations($product, $data);
            $this->syncAttributeValues($product, $request->input('attribute_values', []));
            $this->storeUploadedImages($request, $product, true);
            $this->recordInitialPriceHistory($product, $request);

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'محصول با موفقیت ایجاد شد.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'خطا در ایجاد محصول.');
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $attributes = Attribute::with('values')->orderBy('position')->get();

        $product->load('images', 'latestPriceHistory');
        $selectedAttributeValuesByAttribute = $product->attributeValues()
            ->get()
            ->groupBy('attribute_id')
            ->map(fn ($rows) => $rows->pluck('attribute_value_id')->map(fn ($id) => (int) $id)->all())
            ->all();

        return view('themes.admin.products.edit', compact(
            'product',
            'categories',
            'attributes',
            'selectedAttributeValuesByAttribute'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $this->normalizeBooleanInputs($request);

        $oldPrice = $product->price;
        $oldComparePrice = $product->compare_price;

        $data = $request->validate([
            'name_fa' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'short_description_fa' => ['nullable', 'string'],
            'short_description_en' => ['nullable', 'string'],
            'description_fa' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'gte:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'meta' => ['nullable', 'json'],
            'attribute_values' => ['nullable', 'array'],
            'attribute_values.*' => ['nullable', 'array'],
            'attribute_values.*.*' => ['nullable', 'integer', 'exists:attribute_values,id'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
            'primary_image_id' => ['nullable', 'integer'],
            'price_change_reason' => ['nullable', 'string', 'max:255'],
        ], [
            'compare_price.gte' => 'قیمت قبل از تخفیف باید بزرگ‌تر یا مساوی قیمت فعلی باشد.',
        ]);

        $data = $this->prepareProductData($request, $data, $product);

        DB::beginTransaction();

        try {
            $product->update($data);

            $this->saveTranslations($product, $data);
            $this->syncAttributeValues($product, $request->input('attribute_values', []));
            $this->removeSelectedImages($product, $data['remove_images'] ?? []);
            $this->storeUploadedImages($request, $product);
            $this->syncPrimaryImage($request, $product);
            $this->recordPriceHistoryIfChanged($product, $oldPrice, $oldComparePrice, $request);

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'محصول به‌روزرسانی شد.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'خطا در به‌روزرسانی محصول.');
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try {
            foreach ($product->images as $img) {
                if (Storage::disk('public')->exists($img->path)) {
                    Storage::disk('public')->delete($img->path);
                }
            }

            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'محصول حذف شد.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'خطا در حذف محصول.');
        }
    }

    private function normalizeBooleanInputs(Request $request): void
    {
        $request->merge([
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ]);
    }

    private function prepareProductData(Request $request, array $data, ?Product $product = null): array
{
    $slugSource = $data['slug'] ?? $data['name_en'] ?? $data['name_fa'] ?? 'product';
    $slug = Str::slug($slugSource ?: 'product');

    if ($slug === '') {
        $slug = 'product';
    }

    $data['slug'] = $this->makeUniqueSlug($slug, $product?->id);
    $data['sku'] = $product?->sku ?: $this->generateSku();
    $data['name'] = $data['name_fa'];
    $data['short_description'] = $data['short_description_fa'] ?? null;
    $data['description'] = $data['description_fa'] ?? null;
    $data['compare_price'] = $this->normalizeComparePrice($data['compare_price'] ?? null, $data['price']);
    $data['meta'] = filled($data['meta'] ?? null) ? json_decode($data['meta'], true) : null;
    $data['is_active'] = $request->boolean('is_active');
    $data['is_featured'] = $request->boolean('is_featured');

    unset(
        $data['name_fa'],
        $data['name_en'],
        $data['short_description_fa'],
        $data['short_description_en'],
        $data['description_fa'],
        $data['description_en'],
        $data['price_change_reason'],
        $data['attribute_values'],
        $data['remove_images'],
        $data['primary_image_id']
    );

    return $data;
}

    private function normalizeComparePrice($comparePrice, $price): ?float
    {
        if ($comparePrice === null || $comparePrice === '') {
            return null;
        }

        return (float) $comparePrice > (float) $price ? (float) $comparePrice : null;
    }

    private function makeUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $baseSlug = $slug;
        $counter = 1;

        while (
            Product::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function generateSku(): string
    {
        do {
            $sku = 'PRD-' . strtoupper(Str::random(8));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    private function storeUploadedImages(Request $request, Product $product, bool $forcePrimaryForFirst = false): void
    {
        $files = $request->file('images', []);

        foreach ($files as $index => $file) {
            $path = $file->store('products', 'public');

            $product->images()->create([
                'path' => $path,
                'position' => ((int) $product->images()->max('position')) + $index + 1,
                'is_primary' => $forcePrimaryForFirst
                    ? $index === 0 && ! $product->images()->where('is_primary', true)->exists()
                    : false,
            ]);
        }

        if (! $product->images()->where('is_primary', true)->exists()) {
            $firstImage = $product->images()->orderBy('position')->first();

            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }
    }

    private function removeSelectedImages(Product $product, array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $images = $product->images()->whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();
        }

        if (! $product->images()->where('is_primary', true)->exists()) {
            $firstImage = $product->images()->orderBy('position')->first();

            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }
    }

    private function syncPrimaryImage(Request $request, Product $product): void
    {
        $primaryImageId = (int) $request->input('primary_image_id');

        if ($primaryImageId < 1) {
            return;
        }

        $product->images()->update(['is_primary' => false]);

        $product->images()
            ->where('id', $primaryImageId)
            ->update(['is_primary' => true]);
    }

    private function recordInitialPriceHistory(Product $product, Request $request): void
    {
        $product->priceHistories()->create([
            'changed_by' => $request->user()?->id,
            'old_price' => null,
            'new_price' => $product->price,
            'old_compare_price' => null,
            'new_compare_price' => $product->compare_price,
            'change_type' => 'initial',
            'reason' => $request->input('price_change_reason') ?: 'ایجاد محصول',
            'meta' => [
                'source' => 'admin.products.store',
            ],
        ]);
    }

    private function recordPriceHistoryIfChanged(Product $product, $oldPrice, $oldComparePrice, Request $request): void
    {
        $newPrice = $product->price;
        $newComparePrice = $product->compare_price;

        $priceChanged = (float) $oldPrice !== (float) $newPrice;
        $comparePriceChanged = (float) ($oldComparePrice ?? 0) !== (float) ($newComparePrice ?? 0);

        if (! $priceChanged && ! $comparePriceChanged) {
            return;
        }

        $product->priceHistories()->create([
            'changed_by' => $request->user()?->id,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'old_compare_price' => $oldComparePrice,
            'new_compare_price' => $newComparePrice,
            'change_type' => 'manual',
            'reason' => $request->input('price_change_reason') ?: 'ویرایش دستی از پنل ادمین',
            'meta' => [
                'source' => 'admin.products.update',
                'price_changed' => $priceChanged,
                'compare_price_changed' => $comparePriceChanged,
            ],
        ]);
    }

    private function saveTranslations(Product $product, array $data): void
    {
        foreach (['fa', 'en'] as $locale) {
            $name = $data['name_' . $locale] ?? null;

            if ($name === null || $name === '') {
                continue;
            }

            ProductTranslation::updateOrCreate(
                ['product_id' => $product->id, 'locale' => $locale],
                [
                    'name' => $name,
                    'short_description' => $data['short_description_' . $locale] ?? null,
                    'description' => $data['description_' . $locale] ?? null,
                    'is_published' => true,
                ]
            );
        }
    }

    private function syncAttributeValues(Product $product, array $attributeValueMap): void
    {
        $product->attributeValues()->delete();

        $attributes = Attribute::query()
            ->whereIn('id', array_map('intval', array_keys($attributeValueMap)))
            ->get()
            ->keyBy('id');

        foreach ($attributeValueMap as $attributeId => $selectedValueIds) {
            $attributeId = (int) $attributeId;
            $attribute = $attributes->get($attributeId);

            if (! $attribute) {
                continue;
            }

            $selectedValueIds = array_values(array_unique(array_filter(array_map('intval', (array) $selectedValueIds))));

            if ($attribute->selection_mode === 'single') {
                $selectedValueIds = array_slice($selectedValueIds, 0, 1);
            }

            $validValues = AttributeValue::query()
                ->where('attribute_id', $attributeId)
                ->whereIn('id', $selectedValueIds)
                ->get();

            foreach ($validValues as $attributeValue) {
                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attributeId,
                    'attribute_value_id' => $attributeValue->id,
                ]);
            }
        }
    }
}