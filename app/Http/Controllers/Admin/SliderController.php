<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $sliders = Slider::query()->orderBy('key')->orderBy('position')->get();

        return view('themes.admin.sliders.index', compact('sliders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:80'],
            'locale' => ['nullable', 'in:fa,en'],
            'title' => ['nullable', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072', 'dimensions:min_width=1200,min_height=500'],
            'mobile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=700,min_height=700'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:500'],
            'link_text' => ['nullable', 'string', 'max:80'],
            'position' => ['nullable', 'integer', 'min:0'],
            'focal_x' => ['nullable', 'integer', 'between:0,100'],
            'focal_y' => ['nullable', 'integer', 'between:0,100'],
            'mobile_focal_x' => ['nullable', 'integer', 'between:0,100'],
            'mobile_focal_y' => ['nullable', 'integer', 'between:0,100'],
            'is_active' => ['sometimes', 'boolean'],
        ], [
            'image.image' => 'فایل تصویر دسکتاپ معتبر نیست.',
            'image.mimes' => 'فرمت تصویر دسکتاپ باید jpg، png یا webp باشد.',
            'image.max' => 'حجم تصویر دسکتاپ نباید بیشتر از 3 مگابایت باشد.',
            'image.dimensions' => 'ابعاد تصویر دسکتاپ حداقل باید 1200x500 باشد.',
            'mobile_image.image' => 'فایل تصویر موبایل معتبر نیست.',
            'mobile_image.mimes' => 'فرمت تصویر موبایل باید jpg، png یا webp باشد.',
            'mobile_image.max' => 'حجم تصویر موبایل نباید بیشتر از 2 مگابایت باشد.',
            'mobile_image.dimensions' => 'ابعاد تصویر موبایل حداقل باید 700x700 باشد.',
        ]);

        $desktopImagePath = $this->storeImage($request, 'image', $data['image_path'] ?? null);
        $mobileImagePath = $this->storeImage($request, 'mobile_image', null);

        if (! $desktopImagePath && ! $mobileImagePath) {
            return redirect()->back()->withInput()->withErrors([
                'image' => 'حداقل تصویر دسکتاپ یا موبایل باید انتخاب شود.',
            ]);
        }

        $desktopImagePath = $desktopImagePath ?: $mobileImagePath;
        $mobileImagePath = $mobileImagePath ?: $desktopImagePath;

        Slider::create([
            'key' => $data['key'],
            'locale' => $data['locale'] ?? null,
            'title' => $data['title'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,
            'image_path' => $desktopImagePath,
            'mobile_image_path' => $mobileImagePath,
            'link_url' => $data['link_url'] ?? null,
            'link_text' => $data['link_text'] ?? null,
            'focal_x' => (int) ($data['focal_x'] ?? 50),
            'focal_y' => (int) ($data['focal_y'] ?? 50),
            'mobile_focal_x' => (int) ($data['mobile_focal_x'] ?? 50),
            'mobile_focal_y' => (int) ($data['mobile_focal_y'] ?? 50),
            'position' => (int) ($data['position'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'اسلاید جدید ایجاد شد.');
    }

    public function update(Request $request, Slider $slider)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:80'],
            'locale' => ['nullable', 'in:fa,en'],
            'title' => ['nullable', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072', 'dimensions:min_width=1200,min_height=500'],
            'mobile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=700,min_height=700'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:500'],
            'link_text' => ['nullable', 'string', 'max:80'],
            'position' => ['nullable', 'integer', 'min:0'],
            'focal_x' => ['nullable', 'integer', 'between:0,100'],
            'focal_y' => ['nullable', 'integer', 'between:0,100'],
            'mobile_focal_x' => ['nullable', 'integer', 'between:0,100'],
            'mobile_focal_y' => ['nullable', 'integer', 'between:0,100'],
            'is_active' => ['sometimes', 'boolean'],
        ], [
            'image.image' => 'فایل تصویر دسکتاپ معتبر نیست.',
            'image.mimes' => 'فرمت تصویر دسکتاپ باید jpg، png یا webp باشد.',
            'image.max' => 'حجم تصویر دسکتاپ نباید بیشتر از 3 مگابایت باشد.',
            'image.dimensions' => 'ابعاد تصویر دسکتاپ حداقل باید 1200x500 باشد.',
            'mobile_image.image' => 'فایل تصویر موبایل معتبر نیست.',
            'mobile_image.mimes' => 'فرمت تصویر موبایل باید jpg، png یا webp باشد.',
            'mobile_image.max' => 'حجم تصویر موبایل نباید بیشتر از 2 مگابایت باشد.',
            'mobile_image.dimensions' => 'ابعاد تصویر موبایل حداقل باید 700x700 باشد.',
        ]);

        $desktopImagePath = $this->storeImage($request, 'image', $data['image_path'] ?? null);
        $mobileImagePath = $this->storeImage($request, 'mobile_image', null);

        if ($request->hasFile('image') && $slider->image_path) {
            $this->deleteManagedImage($slider->image_path);
        }

        if ($request->hasFile('mobile_image') && $slider->mobile_image_path) {
            $this->deleteManagedImage($slider->mobile_image_path);
        }

        $slider->update([
            'key' => $data['key'],
            'locale' => $data['locale'] ?? null,
            'title' => $data['title'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,
            'image_path' => $desktopImagePath ?: $slider->image_path,
            'mobile_image_path' => $mobileImagePath ?: ($slider->mobile_image_path ?: ($desktopImagePath ?: $slider->image_path)),
            'link_url' => $data['link_url'] ?? null,
            'link_text' => $data['link_text'] ?? null,
            'focal_x' => (int) ($data['focal_x'] ?? 50),
            'focal_y' => (int) ($data['focal_y'] ?? 50),
            'mobile_focal_x' => (int) ($data['mobile_focal_x'] ?? 50),
            'mobile_focal_y' => (int) ($data['mobile_focal_y'] ?? 50),
            'position' => (int) ($data['position'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'اسلاید به روز شد.');
    }

    public function destroy(Slider $slider)
    {
        $this->deleteManagedImage($slider->image_path);
        $this->deleteManagedImage($slider->mobile_image_path);
        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('success', 'اسلاید حذف شد.');
    }

    private function storeImage(Request $request, string $field, ?string $fallbackPath = null): ?string
    {
        if (! $request->hasFile($field)) {
            return $fallbackPath;
        }

        $path = $request->file($field)->store('sliders', 'public');

        return 'storage/' . $path;
    }

    private function deleteManagedImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'storage/sliders/')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('storage/', '', $path));
    }
}
