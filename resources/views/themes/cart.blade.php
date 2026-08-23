@extends('themes.default.layouts.shop')
@section('title', 'سبد خرید')
@endsection
@section('content')
@php $cart = session('cart', collect()); @endphp
{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/cart.blade.php --}}

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>سبد خرید</h2>
    <a href="{{ route('shop.index') }}" class="btn btn-secondary">بازگشت به فروشگاه</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($cart->isEmpty())
    <div class="alert alert-info">سبد خرید شما خالی است.</div>
@else

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th style="width: 80px">تصویر</th>
            <th>محصول</th>
            <th>قیمت واحد</th>
            <th style="width: 120px">تعداد</th>
            <th>قیمت کل</th>
            <th style="width: 100px">عملیات</th>
        </tr>
    </thead>
    <tbody>

        @foreach($cart->items() as $item)
            <tr>

                {{-- تصویر --}}
                <td>
                    @if($item->product)
                        <img src="{{ $item->product->image_url }}"
                             alt="{{ $item->product->name }}"
                             style="width:60px; height:60px; object-fit:contain; background:#f0f0f0;">
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>

                {{-- نام محصول --}}
                <td>{{ $item->product->name ?? '—' }}</td>

                {{-- قیمت واحد --}}
                <td>{{ number_format($item->price) }} تومان</td>

                {{-- تعداد --}}
                <td>
                    <form action="{{ route('cart.update', $item->product_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="number"
                               name="quantity"
                               value="{{ $item->quantity }}"
                               min="1"
                               class="form-control form-control-sm"
                               style="width:70px; display:inline-block;">

                        <button class="btn btn-sm btn-primary mt-1">بروزرسانی</button>
                    </form>
                </td>

                {{-- قیمت کل --}}
                <td>{{ number_format($item->total) }} تومان</td>

                {{-- حذف --}}
                <td>
                    <form action="{{ route('cart.remove', $item->product_id) }}"
                          method="POST"
                          onsubmit="return confirm('حذف این آیتم؟');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">حذف</button>
                    </form>
                </td>

            </tr>
        @endforeach

    </tbody>
</table>

{{-- جمع کل --}}
<div class="card mt-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <h4>جمع کل: {{ number_format($cart->total()) }} تومان</h4>

        <div>
            <a href="{{ route('shop.index') }}" class="btn btn-secondary">ادامه خرید</a>
            <a href="{{ route('checkout.index') }}" class="btn btn-success">تسویه حساب</a>
        </div>
    </div>
</div>

@endif

@endsection
