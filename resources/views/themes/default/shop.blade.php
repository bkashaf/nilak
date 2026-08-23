{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/default/shop.blade.php --}}
@extends('themes.default.layouts.shop')

@section('title', 'فروشگاه')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>فروشگاه</h2>
</div>

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
