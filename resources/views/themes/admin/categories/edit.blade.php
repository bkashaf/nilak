{{-- View: C:/xampp/htdocs/nilak/resources/views/admin/categories/edit.blade.php --}}
@extends('themes.admin.layouts.master')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>ویرایش دسته‌بندی: {{ $category->name }}</h2>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">بازگشت</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>خطا!</strong> لطفاً موارد زیر را بررسی کنید:
        <ul class="mt-2 mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header">اطلاعات اصلی</div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">نام فارسی *</label>
                <input type="text" name="name_fa" class="form-control"
                       value="{{ old('name_fa', $category->translation('fa')?->name ?? $category->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">نام انگلیسی</label>
                <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $category->translation('en')?->name) }}" dir="ltr">
            </div>

            <div class="mb-3">
                <label class="form-label">اسلاگ (اختیاری)</label>
                <input type="text" name="slug" class="form-control"
                       value="{{ old('slug', $category->slug) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">توضیحات فارسی</label>
                <textarea name="description_fa" class="form-control" rows="3">{{ old('description_fa', $category->translation('fa')?->description ?? $category->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">توضیحات انگلیسی</label>
                <textarea name="description_en" class="form-control" rows="3" dir="ltr">{{ old('description_en', $category->translation('en')?->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">تصویر دسته</label><br>
                @if($category->image)
                    <img src="{{ $category->image_url }}" alt="" style="max-width:120px;max-height:120px;object-fit:contain;background:#f0f0f0;border-radius:8px;" class="mb-2"><br>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                        <label class="form-check-label" for="remove_image">حذف تصویر فعلی</label>
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">والد و وضعیت</div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">دستهٔ والد</label>
                <select name="parent_id" class="form-select">
                    <option value="">— بدون والد —</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}"
                            {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->localized_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox"
                       name="status" id="status"
                       {{ old('status', $category->status) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">فعال باشد</label>
            </div>

            <div class="mb-3">
                <label class="form-label">ترتیب نمایش (position)</label>
                <input type="number" name="position" class="form-control"
                       value="{{ old('position', $category->position) }}">
            </div>

        </div>
    </div>

    <button type="submit" class="btn btn-success">ذخیره تغییرات</button>

</form>

@endsection
