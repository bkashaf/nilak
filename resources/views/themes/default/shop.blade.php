{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/default/shop.blade.php --}}
@extends('themes.default.layouts.shop')

@section('title', 'فروشگاه')

@section('content')

@include('themes.default.partials.hero-slider', ['slides' => $shopSlider ?? collect(), 'sliderId' => 'shopHeroSlider'])

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $pageTitle ?? 'فروشگاه' }}</h2>
</div>

<form method="GET" action="{{ route('shop.index') }}" class="card shadow-sm mb-4">
    <div class="card-body">
        <h3 class="h5">فیلتر محصولات</h3>
        <div class="row g-3">
            @foreach($filterableAttributes ?? [] as $attribute)
                <div class="col-md-4">
                    <label for="attribute-{{ $attribute->slug }}" class="form-label">{{ $attribute->name }}</label>
                    <select id="attribute-{{ $attribute->slug }}" name="attributes[{{ $attribute->slug }}]" class="form-select">
                        <option value="">همه</option>
                        @foreach($attribute->values as $value)
                            <option value="{{ $value->id }}" @selected(request('attributes.' . $attribute->slug) == $value->id)>{{ $value->value }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach
            <div class="col-md-4">
                <label for="sort" class="form-label">مرتب‌سازی</label>
                <select id="sort" name="sort" class="form-select">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>جدیدترین</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>قدیمی‌ترین</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>ارزان‌ترین</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>گران‌ترین</option>
                    <option value="discount" @selected(request('sort') === 'discount')>بیشترین تخفیف</option>
                </select>
            </div>
            <div class="col-md-8">
                @php
                    $selectedMinPrice = (int) request('price_min', $priceBounds['min']);
                    $selectedMaxPrice = (int) request('price_max', $priceBounds['max']);
                @endphp
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0">بازه قیمت</label>
                    <small class="text-muted"><span id="price_min_output">{{ number_format($selectedMinPrice) }}</span> تا <span id="price_max_output">{{ number_format($selectedMaxPrice) }}</span> تومان</small>
                </div>
                <div class="price-range" aria-label="بازه قیمت">
                    <div class="price-range-track"></div>
                    <input id="price_min_range" name="price_min" type="range" min="{{ $priceBounds['min'] }}" max="{{ $priceBounds['max'] }}" value="{{ $selectedMinPrice }}" aria-label="حداقل قیمت">
                    <input id="price_max_range" name="price_max" type="range" min="{{ $priceBounds['min'] }}" max="{{ $priceBounds['max'] }}" value="{{ $selectedMaxPrice }}" aria-label="حداکثر قیمت">
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">اعمال فیلتر</button>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary ms-2">پاک‌کردن</a>
            </div>
        </div>
    </div>
</form>

@if($products->count() === 0)
    <div class="alert alert-info">هیچ محصولی یافت نشد.</div>
@endif

<div class="row">

    @foreach($products as $product)
        <div class="col-6 col-md-3 mb-4">
            <x-product-card :product="$product" cta-class="btn-primary" />
        </div>
    @endforeach

</div>

<div class="mt-3">
    {{ $products->links() }}
</div>

@endsection
