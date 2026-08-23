@extends('themes.default.layouts.shop')
@section('title', 'تسویه حساب')
@endsection
@section('content')
@php $cart = session('cart', collect()); @endphp
{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/checkout.blade.php --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>تسویه حساب</h2>
    <a href="{{ route('cart.index') }}" class="btn btn-secondary">بازگشت به سبد خرید</a>
</div>

@if($cart->isEmpty())
    <div class="alert alert-info">سبد خرید شما خالی است.</div>
@else

<div class="row">

    {{-- اطلاعات سفارش --}}
    <div class="col-md-7">

        <div class="card mb-4">
            <div class="card-header">اطلاعات گیرنده</div>
            <div class="card-body">

                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">نام و نام خانوادگی *</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">شماره موبایل *</label>
                        <input type="text" name="mobile" class="form-control"
                               value="{{ old('mobile') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">آدرس کامل *</label>
                        <textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">توضیحات سفارش (اختیاری)</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100">
                        ثبت سفارش و ادامه پرداخت
                    </button>

                </form>

            </div>
        </div>

    </div>

    {{-- خلاصه سفارش --}}
    <div class="col-md-5">

        <div class="card">
            <div class="card-header">خلاصه سفارش</div>
            <div class="card-body">

                <table class="table">
                    <tbody>

                        @foreach($cart->items() as $item)
                            <tr>
                                <td>{{ $item->product->name ?? '—' }}</td>
                                <td>{{ $item->quantity }} عدد</td>
                                <td>{{ number_format($item->total) }} تومان</td>
                            </tr>
                        @endforeach

                        <tr class="table-secondary">
                            <td colspan="2"><strong>جمع کل</strong></td>
                            <td><strong>{{ number_format($cart->total()) }} تومان</strong></td>
                        </tr>

                    </tbody>
                </table>

            </div>
        </div>

    </div>

</div>

@endif

@endsection
