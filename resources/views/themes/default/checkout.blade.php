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
        @guest
            <div class="alert alert-warning">برای ثبت سفارش ابتدا وارد حساب کاربری شوید.</div>
            <a href="{{ route('login') }}" class="btn btn-primary">ورود به حساب</a>
        @else
        <form method="POST" action="{{ route('checkout.process') }}">
            @csrf
            <div class="mb-3">
                <label for="address" class="form-label">آدرس تحویل</label>
                <textarea id="address" name="address" class="form-control" rows="4" required>{{ old('address') }}</textarea>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">روش پرداخت</label>
                <select id="payment_method" name="payment_method" class="form-select" required>
                    <option value="">انتخاب روش پرداخت</option>
                    @foreach($paymentMethods as $paymentMethod)
                        <option value="{{ $paymentMethod->name }}" @selected(old('payment_method') === $paymentMethod->name)>
                            {{ $paymentMethod->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">ثبت سفارش و ادامه</button>
        </form>
        @endguest
    @endif
</div>
@endsection
