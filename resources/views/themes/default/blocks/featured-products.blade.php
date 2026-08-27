<div class="block-featured-products">

    {{-- عنوان بلوک --}}
    @if(!empty($data['title']))
        <h2 class="block-title">{{ $data['title'] }}</h2>
    @endif

    {{-- توضیحات بلوک --}}
    @if(!empty($data['description']))
        <p class="block-description">{{ $data['description'] }}</p>
    @endif

    {{-- لیست محصولات --}}
    @if(!empty($data['products']) && is_array($data['products']))
        <div class="product-grid">
            @foreach($data['products'] as $product)
                <div class="product-item">
                    <h3>{{ $product['name'] }}</h3>

                    @if(!empty($product['image']))
                        <img src="{{ asset('storage/' . $product['image']) }}" alt="{{ $product['name'] }}">
                    @endif

                    @if(!empty($product['price']))
                        <p class="price">{{ number_format($product['price']) }} تومان</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>
