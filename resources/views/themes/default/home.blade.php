@extends('themes.default.layouts.shop')

@section('title')
خانه
@endsection

@section('content')

@php
    $defaultImage = asset('themes/default/images/no-image.svg');
    $slides = ($homeSlider ?? collect())->count() ? $homeSlider : collect([
        (object) [
            'title' => 'کالکشن جدید پاییز',
            'subtitle' => 'استایل خودت را بساز',
            'description' => null,
            'image_path' => file_exists(public_path('images/hero-fashion.jpg')) ? 'images/hero-fashion.jpg' : null,
            'mobile_image_path' => null,
            'link_url' => route('shop.index'),
            'link_text' => 'مشاهده محصولات',
            'focal_x' => 50,
            'focal_y' => 50,
            'mobile_focal_x' => 50,
            'mobile_focal_y' => 50,
        ]
    ]);
@endphp

{{-- Hero Section --}}
@include('themes.default.partials.hero-slider', ['slides' => $slides, 'sliderId' => 'homeHeroSlider'])

{{-- Categories --}}
<h2 class="mb-4 fw-bold">دسته‌بندی‌ها</h2>
<div class="row text-center mb-3">
    @forelse($featuredCategories as $category)
        <div class="col-6 col-md-3 mb-3">
            <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="text-decoration-none text-dark">
                <div class="card h-100">
                    <img src="{{ $category->image_url }}" alt="{{ $category->localized_name }}" class="card-img-top" style="height:120px; object-fit:contain; background:var(--color-surface-alt);">
                    <div class="card-body py-2">
                        <span class="fw-bold">{{ $category->localized_name }}</span>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12 text-muted">هنوز دسته‌بندی‌ای برای نمایش تعریف نشده است.</div>
    @endforelse
</div>
<div class="text-center mb-5">
    <a href="{{ route('shop.index') }}" class="btn btn-outline-dark btn-sm">مشاهده همه محصولات</a>
</div>

{{-- New Arrivals --}}
<h2 class="mb-4 fw-bold">جدیدترین محصولات</h2>
<div class="row mb-5">
    @foreach($newProducts as $product)
        <div class="col-6 col-md-3 mb-4">
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
        <div class="col-6 col-md-3 mb-4">
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
<div class="bg-dark text-white p-3 rounded text-center mb-5">
    <h3 class="fw-bold mb-2">عضویت در خبرنامه</h3>
    <p class="mb-2">از محصولات و تخفیف‌های جدید مطلع شوید</p>
    <form class="d-flex justify-content-center">
        <input type="email" class="form-control w-50 me-2 py-1" placeholder="ایمیل شما">
        <button class="btn btn-light">عضویت</button>
    </form>
</div>

@endsection
