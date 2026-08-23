<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_fa' => ['required','string','max:255'],
            'name_en' => ['nullable','string','max:255'],
            'short_description_fa' => ['nullable','string'],
            'short_description_en' => ['nullable','string'],
            'description_fa' => ['nullable','string'],
            'description_en' => ['nullable','string'],
            'slug' => ['nullable','string','max:255','unique:products,slug'],
            'price' => ['required','numeric','min:0'],
            'compare_price' => ['nullable','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'is_active' => ['sometimes','boolean'],
            'is_featured' => ['sometimes','boolean'],
            'category_id' => ['nullable','exists:categories,id'],
            'meta' => ['nullable','array'],
            'images.*' => ['nullable','image','max:5120'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.uniqid();
        }

        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        $data['meta'] = $request->meta ? json_encode($request->meta) : null;

        DB::beginTransaction();
        try {
            $data['name'] = $data['name_fa'];
            $data['short_description'] = $data['short_description_fa'] ?? null;
            $data['description'] = $data['description_fa'] ?? null;
            $product = Product::create($data);
            $this->saveTranslations($product, $data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $idx => $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create([
                        'path' => $path,
                        'is_primary' => $idx === 0,
                        'position' => $idx,
                    ]);
                }
            }

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
        $product->load('images');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name_fa' => ['required','string','max:255'],
            'name_en' => ['nullable','string','max:255'],
            'short_description_fa' => ['nullable','string'],
            'short_description_en' => ['nullable','string'],
            'description_fa' => ['nullable','string'],
            'description_en' => ['nullable','string'],
            'slug' => ['nullable','string','max:255', Rule::unique('products','slug')->ignore($product->id)],
            'price' => ['required','numeric','min:0'],
            'compare_price' => ['nullable','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'is_active' => ['sometimes','boolean'],
            'is_featured' => ['sometimes','boolean'],
            'category_id' => ['nullable','exists:categories,id'],
            'meta' => ['nullable','array'],
            'images.*' => ['nullable','image','max:5120'],
            'remove_images' => ['nullable','array'],
            'remove_images.*' => ['integer'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.uniqid();
        }

        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        $data['meta'] = $request->meta ? json_encode($request->meta) : $product->meta;

        DB::beginTransaction();
        try {
            $data['name'] = $data['name_fa'];
            $data['short_description'] = $data['short_description_fa'] ?? null;
            $data['description'] = $data['description_fa'] ?? null;
            $product->update($data);
            $this->saveTranslations($product, $data);

            if (!empty($data['remove_images'])) {
                $imagesToRemove = $product->images()->whereIn('id', $data['remove_images'])->get();
                foreach ($imagesToRemove as $img) {
                    if (Storage::disk('public')->exists($img->path)) {
                        Storage::disk('public')->delete($img->path);
                    }
                    $img->delete();
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $idx => $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create([
                        'path' => $path,
                        'is_primary' => false,
                        'position' => time() + $idx,
                    ]);
                }
            }

            if ($request->filled('primary_image_id')) {
                $primaryId = (int)$request->input('primary_image_id');
                $product->images()->update(['is_primary' => false]);
                $product->images()->where('id', $primaryId)->update(['is_primary' => true]);
            } else {
                if ($product->images()->where('is_primary', true)->count() === 0) {
                    $first = $product->images()->orderBy('position')->first();
                    if ($first) {
                        $first->update(['is_primary' => true]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'محصول به‌روزرسانی شد.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'خطا در به‌روزرسانی محصول.');
        }
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
            return redirect()->route('admin.products.index')->with('error', 'خطا در حذف محصول.');
        }
    }
}
