@extends('themes.default.layouts.shop')

@section('title', 'پیگیری سفارش')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3">پیگیری سفارش</h1>
                    <p class="text-muted">شماره پیگیری سفارش خود را وارد کنید.</p>

                    <form method="POST" action="{{ route('orders.track') }}" class="row g-2 mb-4">
                        @csrf
                        <div class="col-md-9">
                            <label for="tracking_code" class="visually-hidden">شماره پیگیری</label>
                            <input id="tracking_code" name="tracking_code" value="{{ old('tracking_code', $trackingCode ?? '') }}" class="form-control" placeholder="مثلاً NLK-20260823-ABC123" required>
                        </div>
                        <div class="col-md-3 d-grid">
                            <button type="submit" class="btn btn-primary">پیگیری</button>
                        </div>
                    </form>

                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    @isset($order)
                        @if($order)
                            @php($payment = $order->payments->sortByDesc('id')->first())
                            <div class="border rounded p-3">
                                <h2 class="h5">سفارش {{ $order->tracking_code }}</h2>
                                <p>{{ __('messages.order_status') }}: <strong>{{ __('messages.order_statuses.' . $order->status) }}</strong></p>
                                <p>{{ __('messages.payment_status') }}: <strong>{{ $payment ? __('messages.payment_statuses.' . $payment->status) : '—' }}</strong></p>
                                <p>مبلغ: <strong>{{ number_format($order->total_amount) }} تومان</strong></p>
                                <p>تاریخ ثبت سفارش: <strong>{{ app(\App\Support\DateFormatter::class)->format($order->created_at) }}</strong></p>
                                <p>آخرین به‌روزرسانی سفارش: <strong>{{ app(\App\Support\DateFormatter::class)->format($order->updated_at) }}</strong></p>
                                @if($payment)
                                    <p>آخرین به‌روزرسانی پرداخت: <strong>{{ app(\App\Support\DateFormatter::class)->format($payment->updated_at) }}</strong></p>
                                @endif
                                @if($order->statusHistories->count())
                                    <hr>
                                    <h3 class="h6">تاریخچه وضعیت سفارش</h3>
                                    <ul class="small mb-2">
                                        @foreach($order->statusHistories->take(5) as $history)
                                            <li>{{ __('messages.order_statuses.' . ($history->from_status ?? 'pending')) }} ← {{ __('messages.order_statuses.' . $history->to_status) }} ({{ app(\App\Support\DateFormatter::class)->format($history->created_at) }})</li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if($payment && $payment->statusHistories->count())
                                    <h3 class="h6">تاریخچه وضعیت پرداخت</h3>
                                    <ul class="small mb-2">
                                        @foreach($payment->statusHistories->take(5) as $history)
                                            <li>{{ __('messages.payment_statuses.' . ($history->from_status ?? 'pending')) }} ← {{ __('messages.payment_statuses.' . $history->to_status) }} ({{ app(\App\Support\DateFormatter::class)->format($history->created_at) }})</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <h3 class="h6 mt-4">اقلام سفارش</h3>
                                <ul class="mb-0">
                                    @foreach($order->items as $item)
                                        <li>{{ $item->product->name ?? 'محصول حذف‌شده' }}، {{ $item->quantity }} عدد</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="alert alert-warning">سفارشی با این شماره پیگیری پیدا نشد.</div>
                        @endif
                    @endisset
                </div>
            </div>
        </div>
    </div>
@endsection
