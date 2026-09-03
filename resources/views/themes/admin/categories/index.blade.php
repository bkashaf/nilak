{{-- View: C:/xampp/htdocs/nilak/resources/views/admin/categories/index.blade.php --}}
@extends('themes.admin.layouts.master')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>مدیریت دسته‌بندی‌ها</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">ایجاد دسته‌بندی جدید</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th style="width: 60px">شناسه</th>
            <th style="width: 70px">تصویر</th>
            <th>نام</th>
            <th>والد</th>
            <th>وضعیت</th>
            <th>ترتیب</th>
            <th style="width: 150px">عملیات</th>
        </tr>
    </thead>
    <tbody>

        @forelse($categories as $cat)
            <tr>
                <td>{{ $cat->id }}</td>

                <td>
                    <img src="{{ $cat->image_url }}" alt="" style="width:44px;height:44px;object-fit:contain;background:#f0f0f0;border-radius:6px;">
                </td>

                <td>{{ $cat->localized_name }}</td>

                <td>{{ $cat->parent?->localized_name ?? '—' }}</td>

                <td>
                    @if($cat->status)
                        <span class="badge bg-success">فعال</span>
                    @else
                        <span class="badge bg-secondary">غیرفعال</span>
                    @endif
                </td>

                <td>{{ $cat->position }}</td>

                <td>
                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-warning">
                        ویرایش
                    </a>

                    <form action="{{ route('admin.categories.destroy', $cat->id) }}"
                          method="POST"
                          style="display:inline-block;"
                          onsubmit="return confirm('آیا از حذف دسته‌بندی مطمئن هستید؟');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">حذف</button>
                    </form>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">هیچ دسته‌بندی‌ای یافت نشد.</td>
            </tr>
        @endforelse

    </tbody>
</table>

<div class="mt-3">
    {{ $categories->links() }}
</div>

@endsection
