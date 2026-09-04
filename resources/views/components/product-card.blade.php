@props(['product', 'ctaClass' => 'btn-primary'])

@php
    $outOfStock = (int) $product->stock <= 0;
@endphp

<div class="product-card card h-100">
    <div class="product-card-media">
        <img
            src="{{ $product->image_url }}"
            alt="{{ $product->localized_name }}"
            class="card-img-top"
            loading="lazy"
        >

        @if($product->has_discount)
            <span class="product-card-badge product-card-badge--discount">
                ٪{{ $product->discount_percent }}-
            </span>
        @elseif($outOfStock)
            <span class="product-card-badge product-card-badge--muted">
                ناموجود
            </span>
        @endif
    </div>

    <div class="card-body d-flex flex-column">
        <h5 class="product-card-title">{{ $product->localized_name }}</h5>

        <p class="product-card-price mb-3">
            <strong>{{ number_format($product->price) }} تومان</strong>

            @if($product->has_discount)
                <span class="product-card-price-old">
                    {{ number_format($product->compare_price) }}
                </span>
            @endif
        </p>

        <a
            href="{{ route('shop.product', $product->slug) }}"
            class="btn {{ $ctaClass }} w-100 mt-auto"
        >
            مشاهده محصول
        </a>
    </div>
</div>