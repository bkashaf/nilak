<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;

class AttributeController extends Controller
{
    /**
     * لیست تمام ویژگی‌ها
     */
    public function index()
    {
        $attributes = Attribute::with('values')->get();
        return response()->json($attributes);
    }

    /**
     * نمایش یک ویژگی به همراه مقادیر آن
     */
    public function show($id)
    {
        $attribute = Attribute::with('values')->findOrFail($id);
        return response()->json($attribute);
    }
}
