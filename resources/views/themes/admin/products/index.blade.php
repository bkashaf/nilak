@extends('themes.admin.layouts.master')

@section('title', 'مدیریت محصولات')

@section('content')
    <h1>مدیریت محصولات</h1>

    @if (session('success'))
        <div style="color:green; margin-bottom:10px;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="color:#b00020; margin-bottom:10px;">{{ session('error') }}</div>
    @endif

    <div style="margin-bottom:12px;">
        <a href="{{ route('admin.products.create') }}">ایجاد محصول جدید</a>
    </div>

    <table border="1" cellpadding="8" cellspacing="0" style="width:100%; text-align:right;">
        <thead>
            <tr>
                <th>شناسه</th>
                <th>تصویر</th>
                <th>عنوان</th>
                <th>دسته</th>
                <th>قیمت</th>
                <th>موجودی</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td style="width:80px; text-align:center;">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width:70px; max-height:70px; object-fit:contain;">
                    </td>
                    <td>{{ $product->title }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->is_active ? 'فعال' : 'غیرفعال' }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product) }}" style="margin-left:8px;">ویرایش</a>

                        @php
                            $canDelete = auth()->check() && auth()->user()->hasRole('admin');
                        @endphp

                        @if ($canDelete)
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('آیا از حذف این محصول مطمئن هستید؟')">حذف</button>
                            </form>
                        @else
                            <span style="color:#666; margin-left:8px;">حذف غیرمجاز</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">هیچ محصولی یافت نشد</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:12px; text-align:center;">
        {{ $products->links() }}
    </div>
@endsection
