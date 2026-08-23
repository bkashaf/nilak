@extends('themes.default.layouts.shop')

@section('title')
خانه
@endsection

@section('content')

@php
    $defaultImage = asset('themes/default/images/no-image.svg');
    $heroImage = file_exists(public_path('images/hero-fashion.jpg'))
        ? asset('images/hero-fashion.jpg')
        : $defaultImage;
    $categoryImages = [
        'men' => file_exists(public_path('images/cat-men.jpg')) ? asset('images/cat-men.jpg') : $defaultImage,
        'women' => file_exists(public_path('images/cat-women.jpg')) ? asset('images/cat-women.jpg') : $defaultImage,
        'shoes' => file_exists(public_path('images/cat-shoes.jpg')) ? asset('images/cat-shoes.jpg') : $defaultImage,
        'accessories' => file_exists(public_path('images/cat-accessories.jpg')) ? asset('images/cat-accessories.jpg') : $defaultImage,
    ];
@endphp

{{-- Hero Section --}}
<div class="position-relative mb-5">
    <img src="{{ $heroImage }}"
         alt="کالکشن جدید پاییز"
         class="w-100 rounded" 
         style="height:420px; object-fit:cover;">
    <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
        <h1 class="fw-bold display-5">کالکشن جدید پاییز</h1>
        <p class="fs-5">استایل خودت را بساز</p>
        <a href="{{ route('shop.index') }}" class="btn btn-light btn-lg mt-3">
            مشاهده محصولات
        </a>
    </div>
</div>

{{-- Categories --}}
<h2 class="mb-4 fw-bold">دسته‌بندی‌ها</h2>
<div class="row text-center mb-5">

    <div class="col-md-3 mb-3">
        <a href="{{ route('shop.index') }}" class="text-decoration-none text-dark">
            <div class="card shadow-sm">
                <img src="{{ $categoryImages['men'] }}" alt="مردانه" class="card-img-top" style="height:180px; object-fit:contain; background:#f0f0f0;">
                <div class="card-body fw-bold">مردانه</div>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-3">
        <a href="{{ route('shop.index') }}" class="text-decoration-none text-dark">
            <div class="card shadow-sm">
                <img src="{{ $categoryImages['women'] }}" alt="زنانه" class="card-img-top" style="height:180px; object-fit:contain; background:#f0f0f0;">
                <div class="card-body fw-bold">زنانه</div>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-3">
        <a href="{{ route('shop.index') }}" class="text-decoration-none text-dark">
            <div class="card shadow-sm">
                <img src="{{ $categoryImages['shoes'] }}" alt="کفش" class="card-img-top" style="height:180px; object-fit:contain; background:#f0f0f0;">
                <div class="card-body fw-bold">کفش</div>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-3">
        <a href="{{ route('shop.index') }}" class="text-decoration-none text-dark">
            <div class="card shadow-sm">
                <img src="{{ $categoryImages['accessories'] }}" alt="اکسسوری" class="card-img-top" style="height:180px; object-fit:contain; background:#f0f0f0;">
                <div class="card-body fw-bold">اکسسوری</div>
            </div>
        </a>
    </div>

</div>

{{-- New Arrivals --}}
<h2 class="mb-4 fw-bold">جدیدترین محصولات</h2>
<div class="row mb-5">
    @foreach($newProducts as $product)
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                 <img src="{{ $product->image_url }}"
                     alt="{{ $product->localized_name }}"
                     class="card-img-top"
                     style="height:220px; object-fit:contain; background:#f0f0f0;">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->localized_name }}</h5>
                    <p class="fw-bold">{{ number_format($product->price) }} تومان</p>
                    <a href="{{ route('shop.product', $product->slug) }}" class="btn btn-primary w-100">
                        مشاهده محصول
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Best Sellers --}}
<h2 class="mb-4 fw-bold">پرفروش‌ترین‌ها</h2>
<div class="row mb-5">
    @foreach($bestSellers as $product)
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                 <img src="{{ $product->image_url }}"
                     alt="{{ $product->localized_name }}"
                     class="card-img-top"
                     style="height:220px; object-fit:contain; background:#f0f0f0;">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->localized_name }}</h5>
                    <p class="fw-bold">{{ number_format($product->price) }} تومان</p>
                    <a href="{{ route('shop.product', $product->slug) }}" class="btn btn-outline-dark w-100">
                        مشاهده محصول
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Newsletter --}}
<div class="bg-dark text-white p-5 rounded text-center mb-5">
    <h3 class="fw-bold mb-3">عضویت در خبرنامه</h3>
    <p class="mb-4">از جدیدترین محصولات و تخفیف‌ها باخبر شوید</p>
    <form class="d-flex justify-content-center">
        <input type="email" class="form-control w-50 me-2" placeholder="ایمیل شما">
        <button class="btn btn-light">عضویت</button>
    </form>
</div>

@endsection
