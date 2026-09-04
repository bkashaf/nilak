<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $attributes = Attribute::with('values')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return view('themes.admin.attributes.index', compact('attributes'));
    }

    public function store(Request $request)
    {
        Attribute::create($this->validatedAttributeData($request));

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'ویژگی ایجاد شد.');
    }

    public function update(Request $request, Attribute $attribute)
    {
        $attribute->update($this->validatedAttributeData($request, $attribute));

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'ویژگی به‌روزرسانی شد.');
    }

    public function destroy(Attribute $attribute)
    {
        $isUsedInProducts = DB::table('product_attribute_values')
            ->where('attribute_id', $attribute->id)
            ->exists();

        if ($isUsedInProducts) {
            return redirect()
                ->route('admin.attributes.index')
                ->with('error', 'این ویژگی به محصول‌ها متصل است و فعلاً قابل حذف نیست.');
        }

        $attribute->delete();

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'ویژگی حذف شد.');
    }

    public function storeValue(Request $request, Attribute $attribute)
    {
        $attribute->values()->create($this->validatedValueData($request, $attribute));

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'مقدار ویژگی اضافه شد.');
    }

    public function updateValue(Request $request, Attribute $attribute, AttributeValue $value)
    {
        $this->ensureValueBelongsToAttribute($attribute, $value);

        $value->update($this->validatedValueData($request, $attribute, $value));

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'مقدار ویژگی به‌روزرسانی شد.');
    }

    public function destroyValue(Attribute $attribute, AttributeValue $value)
    {
        $this->ensureValueBelongsToAttribute($attribute, $value);

        $isUsedInProducts = DB::table('product_attribute_values')
            ->where('attribute_value_id', $value->id)
            ->exists();

        if ($isUsedInProducts) {
            return redirect()
                ->route('admin.attributes.index')
                ->with('error', 'این مقدار در محصول‌ها استفاده شده و فعلاً قابل حذف نیست.');
        }

        $value->delete();

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'مقدار ویژگی حذف شد.');
    }

    private function validatedAttributeData(Request $request, ?Attribute $attribute = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'nullable',
                'alpha_dash',
                'max:100',
                Rule::unique('attributes', 'slug')->ignore($attribute?->id),
            ],
            'type' => ['required', 'in:select,text,number,boolean'],
            'selection_mode' => ['required', 'in:single,multiple'],
            'display_mode' => ['required', 'in:dropdown,swatch,chip,toggle'],
            'is_filterable' => ['sometimes', 'boolean'],
            'is_required' => ['sometimes', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
            'config' => ['nullable', 'json'],
        ]);

        if (filled($data['slug'] ?? null)) {
            $data['slug'] = $data['slug'];
        } elseif ($attribute) {
            $data['slug'] = $attribute->slug;
        } else {
            $data['slug'] = $this->makeFallbackSlug($data['name'], 'attribute');
        }

        $data['is_filterable'] = $request->boolean('is_filterable');
        $data['is_required'] = $request->boolean('is_required');
        $data['position'] = $data['position'] ?? ($attribute?->position ?? (((int) Attribute::max('position')) + 1));
        $data['config'] = $this->decodeJson($data['config'] ?? null);

        return $data;
    }

    private function validatedValueData(Request $request, Attribute $attribute, ?AttributeValue $value = null): array
    {
        $data = $request->validate([
            'value' => [
                'required',
                'string',
                'max:100',
                Rule::unique('attribute_values', 'value')
                    ->where(fn ($query) => $query->where('attribute_id', $attribute->id))
                    ->ignore($value?->id),
            ],
            'slug' => [
                'nullable',
                'alpha_dash',
                'max:100',
                Rule::unique('attribute_values', 'slug')
                    ->where(fn ($query) => $query->where('attribute_id', $attribute->id))
                    ->ignore($value?->id),
            ],
            'color_hex' => ['nullable', 'regex:/^#?[0-9a-fA-F]{3,8}$/'],
            'meta' => ['nullable', 'json'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        if (filled($data['slug'] ?? null)) {
            $data['slug'] = $data['slug'];
        } elseif ($value) {
            $data['slug'] = $value->slug;
        } else {
            $data['slug'] = $this->makeFallbackSlug($data['value'], 'value');
        }

        $data['color_hex'] = $this->normalizeColorHex($data['color_hex'] ?? null);
        $data['meta'] = $this->decodeJson($data['meta'] ?? null);
        $data['position'] = $data['position'] ?? ($value?->position ?? (((int) $attribute->values()->max('position')) + 1));

        return $data;
    }

    private function decodeJson(?string $json): ?array
    {
        if (! filled($json)) {
            return null;
        }

        return json_decode($json, true);
    }

    private function normalizeColorHex(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        return '#' . ltrim(trim($value), '#');
    }

    private function makeFallbackSlug(string $source, string $prefix): string
    {
        $slug = Str::slug($source);

        if ($slug === '') {
            $slug = $prefix;
        }

        return $slug . '-' . Str::lower(Str::random(5));
    }

    private function ensureValueBelongsToAttribute(Attribute $attribute, AttributeValue $value): void
    {
        abort_if($value->attribute_id !== $attribute->id, 404);
    }
}