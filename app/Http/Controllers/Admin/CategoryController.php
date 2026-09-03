<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $categories = Category::with('parent')
            ->orderBy('position')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('themes.admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('themes.admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_fa'      => ['required','string','max:255'],
            'name_en'      => ['nullable','string','max:255'],
            'description_fa' => ['nullable','string'],
            'description_en' => ['nullable','string'],
            'slug'        => ['nullable','string','max:255','unique:categories,slug'],
            'image'       => ['nullable','image','max:2048'],
            'parent_id'   => ['nullable','exists:categories,id'],
            'status'      => ['sometimes','boolean'],
            'position'    => ['nullable','integer','min:0'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.uniqid();
        }

        $data['status'] = $request->has('status') ? 1 : 0;
        $data['position'] = $data['position'] ?? 0;

        $data['name'] = $data['name_fa'];
        $data['description'] = $data['description_fa'] ?? null;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);
        $this->saveTranslations($category, $data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت ایجاد شد.');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('themes.admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name_fa'      => ['required','string','max:255'],
            'name_en'      => ['nullable','string','max:255'],
            'description_fa' => ['nullable','string'],
            'description_en' => ['nullable','string'],
            'slug'        => ['nullable','string','max:255', Rule::unique('categories','slug')->ignore($category->id)],
            'image'       => ['nullable','image','max:2048'],
            'remove_image' => ['sometimes','boolean'],
            'parent_id'   => ['nullable','exists:categories,id'],
            'status'      => ['sometimes','boolean'],
            'position'    => ['nullable','integer','min:0'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.uniqid();
        }

        $data['status'] = $request->has('status') ? 1 : 0;
        $data['position'] = $data['position'] ?? $category->position;

        $data['name'] = $data['name_fa'];
        $data['description'] = $data['description_fa'] ?? null;

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = null;
        }

        $category->update($data);
        $this->saveTranslations($category, $data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی به‌روزرسانی شد.');
    }

    public function destroy(Category $category)
    {
        $childrenCount = $category->children()->count();
        $productsCount = $category->products()->count();

        if ($childrenCount > 0 || $productsCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'ابتدا زیرمجموعه‌ها یا محصولات مرتبط را حذف یا منتقل کنید.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی حذف شد.');
    }

    private function saveTranslations(Category $category, array $data): void
    {
        foreach (['fa', 'en'] as $locale) {
            $name = $data['name_' . $locale] ?? null;
            if ($name === null || $name === '') {
                continue;
            }

            CategoryTranslation::updateOrCreate(
                ['category_id' => $category->id, 'locale' => $locale],
                [
                    'name' => $name,
                    'description' => $data['description_' . $locale] ?? null,
                    'is_published' => true,
                ]
            );
        }
    }
}
