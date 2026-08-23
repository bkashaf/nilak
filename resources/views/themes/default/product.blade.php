{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/default/product.blade.php --}}
@extends('themes.default.layouts.shop')

@section('title', $product->localized_name)

@section('content')

<div class="mb-4">
    <a href="{{ route('shop.index') }}" class="btn btn-secondary">بازگشت به فروشگاه</a>
</div>

<div class="row">

    {{-- ستون تصاویر --}}
    <div class="col-md-5">

           {{-- تصویر اصلی با fallback برای فایل ناموجود --}}
           <img src="{{ $product->image_url }}"
               alt="{{ $product->localized_name }}"
               class="img-fluid rounded shadow-sm mb-3"
               style="width:100%; height:350px; object-fit:contain; background:#f0f0f0;">

        {{-- گالری تصاویر --}}
        @if($product->images->count() > 1)
            <div class="d-flex flex-wrap">
                @foreach($product->images as $img)
                    <img src="{{ asset('storage/'.$img->path) }}"
                         class="img-thumbnail me-2 mb-2"
                         style="width:80px; height:80px; object-fit:cover;">
                @endforeach
            </div>
        @endif

    </div>

    {{-- ستون اطلاعات محصول --}}
    <div class="col-md-7">

        <h2 class="mb-3">{{ $product->localized_name }}</h2>

        {{-- قیمت --}}
        <div class="mb-3">
            <strong class="fs-4">{{ number_format($product->price) }} تومان</strong>

            @if($product->compare_price)
                <span class="text-decoration-line-through text-muted ms-2">
                    {{ number_format($product->compare_price) }}
                </span>
            @endif
        </div>

        {{-- دسته‌بندی --}}
        <p class="mb-2">
            <strong>دسته‌بندی:</strong>
            {{ $product->category?->localized_name ?? '—' }}
        </p>

        {{-- موجودی --}}
        <p class="mb-3">
            <strong>موجودی:</strong>
            {{ $product->stock > 0 ? $product->stock.' عدد' : 'ناموجود' }}
        </p>

        {{-- توضیح کوتاه --}}
        @if($product->localized_short_description)
            <p class="text-muted">{{ $product->localized_short_description }}</p>
        @endif

        {{-- توضیحات کامل --}}
        @if($product->localized_description)
            <div class="mt-3">
            {!! nl2br(e($product->localized_description)) !!}
            </div>
        @endif

        {{-- دکمه افزودن به سبد --}}
        <form action="{{ route('cart.add') }}" method="POST" class="mt-4">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="input-group mb-2">
                <label class="input-group-text" for="quantity">تعداد</label>
                <input id="quantity" type="number" name="qty" value="1" min="1" max="{{ max(1, $product->stock) }}" class="form-control" {{ $product->stock < 1 ? 'disabled' : '' }}>
            </div>
            <button type="submit" class="btn btn-success btn-lg w-100" {{ $product->stock < 1 ? 'disabled' : '' }}>
                افزودن به سبد خرید
            </button>
        </form>

    </div>

</div>

@endsection
