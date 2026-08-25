<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $pages = Page::query()->latest('id')->paginate(15);

        return view('themes.admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('themes.admin.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:pages,slug'],
            'content' => ['nullable', 'string'],
            'menu_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
            'show_in_menu' => ['sometimes', 'boolean'],
        ]);

        $slug = $data['slug'] ?? Str::slug($data['title']);
        if (! $slug) {
            $slug = 'page-' . Str::lower(Str::random(6));
        }

        Page::create([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'] ?? null,
            'menu_order' => (int) ($data['menu_order'] ?? 0),
            'is_published' => $request->boolean('is_published'),
            'show_in_menu' => $request->boolean('show_in_menu'),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'صفحه جدید ایجاد شد.');
    }

    public function edit(Page $page)
    {
        return view('themes.admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', 'unique:pages,slug,' . $page->id],
            'content' => ['nullable', 'string'],
            'menu_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
            'show_in_menu' => ['sometimes', 'boolean'],
        ]);

        $page->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'] ?? null,
            'menu_order' => (int) ($data['menu_order'] ?? 0),
            'is_published' => $request->boolean('is_published'),
            'show_in_menu' => $request->boolean('show_in_menu'),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'صفحه به روز شد.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'صفحه حذف شد.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'file.image' => 'فایل انتخابی تصویر معتبر نیست.',
            'file.mimes' => 'فرمت تصویر باید jpg، png یا webp باشد.',
            'file.max' => 'حجم تصویر نباید بیشتر از 4 مگابایت باشد.',
        ]);

        $path = $request->file('file')->store('page-builder', 'public');

        return response()->json([
            'location' => asset('storage/' . $path),
            'path' => 'storage/' . $path,
        ]);
    }
}
