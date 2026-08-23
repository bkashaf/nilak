{{-- View: C:/xampp/htdocs/nilak/resources/views/admin/categories/create.blade.php --}}
@extends('themes.admin.layouts.master')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>ایجاد دسته‌بندی جدید</h2>
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

<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf

    <div class="card mb-4">
        <div class="card-header">اطلاعات اصلی</div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">نام دسته‌بندی *</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">اسلاگ (اختیاری)</label>
                <input type="text" name="slug" class="form-control"
                       value="{{ old('slug') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">توضیحات</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
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
                            {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox"
                       name="status" id="status"
                       {{ old('status', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="status">فعال باشد</label>
            </div>

            <div class="mb-3">
                <label class="form-label">ترتیب نمایش (position)</label>
                <input type="number" name="position" class="form-control"
                       value="{{ old('position', 0) }}">
            </div>

        </div>
    </div>

    <button type="submit" class="btn btn-success">ثبت دسته‌بندی</button>

</form>

@endsection
