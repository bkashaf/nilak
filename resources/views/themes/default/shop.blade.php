{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/default/shop.blade.php --}}
@extends('themes.default.layouts.shop')

@section('title', 'فروشگاه')

@section('content')

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
            <div class="col-md-4">
                <label for="price_min" class="form-label">حداقل قیمت</label>
                <input id="price_min" name="price_min" type="number" min="0" value="{{ request('price_min') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="price_max" class="form-label">حداکثر قیمت</label>
                <input id="price_max" name="price_max" type="number" min="0" value="{{ request('price_max') }}" class="form-control">
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
        <div class="col-md-3 mb-4">

            <div class="card h-100 shadow-sm">

                 {{-- تصویر محصول --}}
                 <img src="{{ $product->image_url }}"
                     alt="{{ $product->localized_name }}"
                     class="card-img-top"
                     style="height:200px; object-fit:contain; background:#f0f0f0;">

                <div class="card-body">

                    {{-- نام محصول --}}
                    <h5 class="card-title">{{ $product->localized_name }}</h5>

                    {{-- قیمت --}}
                    <p class="card-text">
                        <strong>{{ number_format($product->price) }} تومان</strong>

                        @if($product->compare_price)
                            <span class="text-decoration-line-through text-muted ms-2">
                                {{ number_format($product->compare_price) }}
                            </span>
                        @endif
                    </p>

                    {{-- لینک صفحه محصول --}}
                    <a href="{{ route('shop.product', $product->slug) }}"
                       class="btn btn-primary w-100 text-white fw-bold">
                        مشاهده محصول
                    </a>

                </div>

            </div>

        </div>
    @endforeach

</div>

<div class="mt-3">
    {{ $products->links() }}
</div>

@endsection
