@extends('themes.default.layouts.shop')

@section('title')
تسویه حساب
@endsection

@section('content')
@php
    $cart = session('cart', collect());
@endphp

<div class="container">
    <h1>تسویه حساب</h1>

    @if($cart->isEmpty())
        <p>سبد خرید شما خالی است. ابتدا محصولات را اضافه کنید.</p>
    @else
        <p>تعداد آیتم‌ها: {{ $cart->count() }}</p>
        <form method="POST" action="{{ route('checkout.index') }}">
            @csrf
            <button type="submit" class="btn btn-primary">ادامه پرداخت</button>
        </form>
    @endif
</div>
@endsection
