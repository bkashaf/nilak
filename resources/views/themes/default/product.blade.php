@extends('themes.default.layouts.shop')

@section('title', $product->localized_name)

@section('content')

@php
    $outOfStock = (int) $product->stock < 1;
    $groupedAttributes = $product->grouped_attribute_values;
    $colorGroup = $groupedAttributes->first(fn ($group) => in_array($group->attribute->slug, ['color', 'colour'], true));
@endphp

<div class="mb-4">
    <a href="{{ route('shop.index') }}" class="btn btn-secondary">بازگشت به فروشگاه</a>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="position-relative mb-3">
                    <img
                        src="{{ $product->image_url }}"
                        alt="{{ $product->localized_name }}"
                        class="img-fluid rounded w-100"
                        style="max-height:520px; object-fit:cover;"
                    >

                    @if($product->has_discount)
                        <span class="badge bg-danger position-absolute top-0 start-0 m-3">
                            {{ $product->discount_percent }}٪ تخفیف
                        </span>
                    @endif
                </div>

                @if($product->images->count() > 1)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($product->images as $img)
                            <img
                                src="{{ asset('storage/' . $img->path) }}"
                                alt="{{ $product->localized_name }}"
                                class="rounded border"
                                style="width:72px; height:72px; object-fit:cover;"
                            >
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h1 class="h3 mb-3">{{ $product->localized_name }}</h1>

                <div class="d-flex align-items-center flex-wrap gap-3 mb-3">
                    <span class="fs-4 fw-bold text-danger">{{ number_format($product->price) }} تومان</span>

                    @if($product->has_discount)
                        <span class="text-muted text-decoration-line-through">{{ number_format($product->compare_price) }}</span>
                        <span class="badge bg-danger">{{ $product->discount_percent }}٪</span>
                    @endif
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <div class="small text-muted mb-1">دسته‌بندی</div>
                            <div>{{ $product->category?->localized_name ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <div class="small text-muted mb-1">وضعیت موجودی</div>
                            <div class="{{ $outOfStock ? 'text-danger' : 'text-success' }}">
                                {{ $outOfStock ? 'ناموجود' : $product->stock . ' عدد موجود' }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($product->localized_short_description)
                    <p class="text-muted mb-4">{{ $product->localized_short_description }}</p>
                @endif

                @if($groupedAttributes->isNotEmpty())
                    <div class="mb-4">
                        @foreach($groupedAttributes as $group)
                            <div class="mb-3">
                                <div class="fw-semibold mb-2">{{ $group->attribute->name }}</div>

                                @if($group->attribute->usesSwatches())
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($group->values as $value)
                                            <span
                                                class="border rounded-circle"
                                                title="{{ $value->value }}"
                                                style="display:inline-block;width:30px;height:30px;background:{{ $value->normalized_color_hex ?: '#f8f9fa' }};"
                                            ></span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($group->values as $value)
                                            <span class="badge rounded-pill text-bg-light border px-3 py-2">{{ $value->value }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="border rounded p-3 bg-light">
                    @if($colorGroup)
                        <div class="mb-3">
                            <div class="small text-muted mb-2">رنگ‌های موجود</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($colorGroup->values as $value)
                                    <div class="d-inline-flex align-items-center gap-2 border rounded-pill px-2 py-1 bg-white">
                                        <span
                                            class="border rounded-circle"
                                            style="display:inline-block;width:18px;height:18px;background:{{ $value->normalized_color_hex ?: '#f8f9fa' }};"
                                        ></span>
                                        <span class="small">{{ $value->value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="d-flex align-items-stretch gap-3 flex-wrap">
                            <div class="input-group" style="max-width: 180px;">
                                <button type="button" class="btn btn-outline-secondary" data-qty-action="increase">+</button>
                                <input
                                    id="quantity"
                                    type="number"
                                    name="qty"
                                    value="1"
                                    min="1"
                                    max="{{ max(1, $product->stock) }}"
                                    class="form-control text-center"
                                    {{ $outOfStock ? 'disabled' : '' }}
                                >
                                <button type="button" class="btn btn-outline-secondary" data-qty-action="decrease">-</button>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-danger btn-lg flex-grow-1"
                                {{ $outOfStock ? 'disabled' : '' }}
                            >
                                {{ $outOfStock ? 'موجود نیست' : 'افزودن به سبد خرید' }}
                            </button>
                        </div>
                    </form>
                </div>

                @if($product->localized_description)
                    <hr class="my-4">
                    <div class="product-detail-description">
                        {!! nl2br(e($product->localized_description)) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const quantityInput = document.getElementById('quantity');
    const buttons = document.querySelectorAll('[data-qty-action]');

    if (!quantityInput) {
        return;
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            const action = button.getAttribute('data-qty-action');
            const min = parseInt(quantityInput.getAttribute('min') || '1', 10);
            const max = parseInt(quantityInput.getAttribute('max') || '1', 10);
            const current = parseInt(quantityInput.value || '1', 10);

            if (action === 'increase') {
                quantityInput.value = Math.min(max, current + 1);
            }

            if (action === 'decrease') {
                quantityInput.value = Math.max(min, current - 1);
            }
        });
    });
});
</script>

@endsection