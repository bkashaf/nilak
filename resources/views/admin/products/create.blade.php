{{-- View: C:/xampp/htdocs/nilak/resources/views/admin/products/create.blade.php --}}
@extends('layouts.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>ایجاد محصول جدید</h2>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">بازگشت</a>
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

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card mb-4">
        <div class="card-header">اطلاعات اصلی</div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">نام محصول *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">اسلاگ (اختیاری)</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">توضیح کوتاه</label>
                <textarea name="short_description" class="form-control" rows="2">{{ old('short_description') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">توضیحات کامل</label>
                <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
            </div>

        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header">قیمت و موجودی</div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">قیمت *</label>
                <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">قیمت قبل از تخفیف (اختیاری)</label>
                <input type="number" name="compare_price" class="form-control" value="{{ old('compare_price') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">موجودی *</label>
                <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}" required>
            </div>

        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header">دسته‌بندی و وضعیت</div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">دسته‌بندی</label>
                <select name="category_id" class="form-select">
                    <option value="">— بدون دسته‌بندی —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                <label class="form-check-label" for="is_active">فعال باشد</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured">
                <label class="form-check-label" for="is_featured">محصول ویژه</label>
            </div>

        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header">اطلاعات اضافی (Meta)</div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">Meta (JSON)</label>
                <textarea name="meta" class="form-control" rows="3" placeholder='{"brand":"Nike"}'>{{ old('meta') }}</textarea>
            </div>

        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header">تصاویر محصول</div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">آپلود تصاویر (چندتایی)</label>
                <input type="file" name="images[]" class="form-control" multiple>
                <small class="text-muted">حداکثر حجم هر تصویر: 5MB</small>
            </div>

        </div>
    </div>


    <button type="submit" class="btn btn-success">ثبت محصول</button>

</form>

@endsection
