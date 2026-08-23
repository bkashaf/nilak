<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $attributes = Attribute::with('values')->orderBy('position')->get();

        return view('themes.admin.attributes.index', compact('attributes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'alpha_dash', 'max:100', 'unique:attributes,slug'],
            'type' => ['required', 'in:select,text,number,boolean'],
            'is_filterable' => ['sometimes', 'boolean'],
            'is_required' => ['sometimes', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']) . '-' . Str::lower(Str::random(5));
        $data['is_filterable'] = $request->boolean('is_filterable');
        $data['is_required'] = $request->boolean('is_required');
        $data['position'] = Attribute::max('position') + 1;
        Attribute::create($data);

        return back()->with('success', 'ویژگی ایجاد شد.');
    }

    public function storeValue(Request $request, Attribute $attribute)
    {
        $data = $request->validate([
            'value' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'alpha_dash', 'max:100'],
        ]);

        $attribute->values()->create([
            'value' => $data['value'],
            'slug' => $data['slug'] ?: Str::slug($data['value']),
            'position' => $attribute->values()->max('position') + 1,
        ]);

        return back()->with('success', 'مقدار ویژگی اضافه شد.');
    }
}
