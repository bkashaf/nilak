<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'slug'        => ['nullable','string','max:255','unique:categories,slug'],
            'description' => ['nullable','string'],
            'parent_id'   => ['nullable','exists:categories,id'],
            'status'      => ['sometimes','boolean'],
            'position'    => ['nullable','integer','min:0'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.uniqid();
        }

        $data['status'] = $request->has('status') ? 1 : 0;
        $data['position'] = $data['position'] ?? 0;

        Category::create($data);

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

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'slug'        => ['nullable','string','max:255', Rule::unique('categories','slug')->ignore($category->id)],
            'description' => ['nullable','string'],
            'parent_id'   => ['nullable','exists:categories,id'],
            'status'      => ['sometimes','boolean'],
            'position'    => ['nullable','integer','min:0'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.uniqid();
        }

        $data['status'] = $request->has('status') ? 1 : 0;
        $data['position'] = $data['position'] ?? $category->position;

        $category->update($data);

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
}
