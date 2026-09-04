@extends('themes.default.layouts.shop')

@section('title', 'پیگیری سفارش')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3">پیگیری سفارش</h1>
                    <p class="text-muted">شماره پیگیری سفارش خود را وارد کنید.</p>

                    <form method="POST" action="{{ route('order.tracking.submit') }}" class="row g-2 mb-4">
                        @csrf
                        <div class="col-md-9">
                            <label for="tracking_code" class="visually-hidden">شماره پیگیری</label>
                            <input
                                id="tracking_code"
                                name="tracking_code"
                                value="{{ old('tracking_code', $trackingCode ?? '') }}"
                                class="form-control"
                                placeholder="مثلاً NLK-20260823-ABC123"
                                required
                            >
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
                            @php($latestReceipt = $payment?->latestBankReceipt)
                            @php($isReceiptPayment = $payment?->isReceiptPayment() ?? false)
                            @php($canUploadReceipt = $payment?->canUploadReceipt() ?? false)
                            @php($isAwaitingReceipt = $payment?->isAwaitingReceipt() ?? false)
                            @php($isUnderReceiptReview = $payment?->isUnderReceiptReview() ?? false)
                            @php($hasUploadedReceipt = $payment?->hasUploadedReceipt() ?? false)

                            <div class="border rounded p-3">
                                <h2 class="h5">سفارش {{ $order->tracking_code }}</h2>
                                <p>{{ __('messages.order_status') }}: <strong>{{ __('messages.order_statuses.' . $order->status) }}</strong></p>
                                <p>{{ __('messages.payment_status') }}: <strong>{{ $payment ? __('messages.payment_statuses.' . $payment->status) : '—' }}</strong></p>
                                <p>روش پرداخت: <strong>{{ $payment?->method?->title ?? '—' }}</strong></p>
                                <p>مبلغ: <strong>{{ number_format($order->total_amount) }} تومان</strong></p>
                                <p>تاریخ ثبت سفارش: <strong>{{ app(\App\Support\DateFormatter::class)->format($order->created_at) }}</strong></p>

                                @if($isReceiptPayment && $payment)
                                    <div class="alert alert-info mt-3">
                                        این سفارش با روش پرداخت رسید بانکی ثبت شده است.
                                        @if($isAwaitingReceipt)
                                            لطفاً برای تکمیل فرایند پرداخت، رسید بانکی خود را بارگذاری کنید.
                                        @elseif($isUnderReceiptReview)
                                            رسید بانکی ثبت شده و در انتظار بررسی مدیر است.
                                        @elseif($payment->status === 'paid')
                                            رسید بانکی توسط مدیر تأیید شده است.
                                        @elseif($payment->status === 'rejected' && $hasUploadedReceipt)
                                            رسید قبلی رد شده است و می‌توانید مجدداً رسید جدید ارسال کنید.
                                        @endif
                                    </div>

                                    @if($latestReceipt)
                                        <div class="border rounded p-3 bg-light mt-3">
                                            <div class="fw-semibold mb-2">آخرین رسید ثبت‌شده</div>
                                            <div class="mb-1"><strong>وضعیت رسید:</strong> {{ $latestReceipt->status }}</div>
                                            <div class="mb-1"><strong>شماره پیگیری بانکی:</strong> {{ $latestReceipt->tracking_number ?: '—' }}</div>

                                            @if($latestReceipt->rejection_reason)
                                                <div class="text-danger mt-2">
                                                    <strong>دلیل رد:</strong> {{ $latestReceipt->rejection_reason }}
                                                </div>
                                            @endif

                                            @if($latestReceipt->file_url)
                                                <div class="mt-2">
                                                    <a href="{{ $latestReceipt->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        مشاهده فایل رسید
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @auth
                                        @if(auth()->id() === $order->user_id)
                                            <div class="d-flex gap-2 flex-wrap mt-3">
                                                <a href="{{ route('account.orders.show', $order) }}" class="btn btn-danger">
                                                    @if($canUploadReceipt)
                                                        {{ $payment->status === 'rejected' ? 'مشاهده سفارش و ارسال مجدد رسید' : 'مشاهده سفارش و ارسال رسید' }}
                                                    @elseif($hasUploadedReceipt)
                                                        مشاهده سفارش و وضعیت رسید
                                                    @else
                                                        مشاهده جزئیات سفارش
                                                    @endif
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-warning mt-3 mb-0">
                                            برای ارسال رسید بانکی، ابتدا وارد حساب کاربری خود شوید.
                                        </div>
                                    @endauth
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