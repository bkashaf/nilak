<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * لیست دسته‌ها (همه دسته‌ها)
     */
    public function index()
    {
        $categories = Category::orderBy('position', 'asc')->get();
        return response()->json($categories);
    }

    /**
     * نمایش یک دسته به همراه زیرمجموعه‌ها
     */
    public function show($id)
    {
        $category = Category::with('children')->findOrFail($id);
        return response()->json($category);
    }
}
