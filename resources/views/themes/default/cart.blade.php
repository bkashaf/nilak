@extends('themes.default.layouts.shop')

@section('title')
سبد خرید
@endsection

@section('content')
@php
    $cart = session('cart', collect());
@endphp

<div class="container">
    <h1>سبد خرید</h1>

    @if($cart->isEmpty())
        <p>سبد خرید شما خالی است.</p>
    @else
        <ul>
            @foreach($cart as $item)
                <li>{{ $item['name'] ?? 'محصول' }} - {{ $item['qty'] ?? 1 }}</li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
