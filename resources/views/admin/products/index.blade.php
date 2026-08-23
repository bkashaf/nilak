{{-- View: C:/xampp/htdocs/nilak/resources/views/admin/products/index.blade.php --}}
@extends('layouts.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>مدیریت محصولات</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">ایجاد محصول جدید</a>
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
            <th style="width: 60px">تصویر</th>
            <th>نام</th>
            <th>دسته‌بندی</th>
            <th>قیمت</th>
            <th>موجودی</th>
            <th>وضعیت</th>
            <th style="width: 150px">عملیات</th>
        </tr>
    </thead>
    <tbody>

        @forelse($products as $product)
            <tr>
                <td>
                    <img src="{{ $product->image_url }}"
                         alt="{{ $product->name }}"
                         style="width:50px; height:50px; object-fit:contain;">
                </td>

                <td>{{ $product->name }}</td>

                <td>{{ $product->category->name ?? '—' }}</td>

                <td>{{ number_format($product->price) }} تومان</td>

                <td>{{ $product->stock }}</td>

                <td>
                    @if($product->is_active)
                        <span class="badge bg-success">فعال</span>
                    @else
                        <span class="badge bg-secondary">غیرفعال</span>
                    @endif
                </td>

                <td>
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                        ویرایش
                    </a>

                    <form action="{{ route('admin.products.destroy', $product->id) }}" 
                          method="POST" 
                          style="display:inline-block;"
                          onsubmit="return confirm('آیا از حذف محصول مطمئن هستید؟');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">حذف</button>
                    </form>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="7" class="text-center text-muted">هیچ محصولی یافت نشد.</td>
            </tr>
        @endforelse

    </tbody>
</table>

<div class="mt-3">
    {{ $products->links() }}
</div>

@endsection
