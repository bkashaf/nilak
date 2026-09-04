@extends('themes.admin.layouts.master')

@section('title', 'ویرایش سفارش')

@section('content')
    @php($isReceiptPayment = $payment?->isReceiptPayment() ?? false)
    @php($isAwaitingReceipt = $payment?->isAwaitingReceipt() ?? false)
    @php($isUnderReview = $payment?->isUnderReceiptReview() ?? false)
    @php($hasUploadedReceipt = $payment?->hasUploadedReceipt() ?? false)
    @php($canReviewReceipt = $isReceiptPayment && $isUnderReview && $latestReceipt && $latestReceipt->status === 'pending_review')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">سفارش #{{ $order->id }}</h1>
            <p class="text-muted mb-0">بررسی جزئیات سفارش، پرداخت و رسید بانکی مشتری</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="mb-2"><strong>مشتری:</strong> {{ $order->user->name ?? $order->user->email ?? '—' }}</p>
                    <p class="mb-2"><strong>ایمیل:</strong> {{ $order->user->email ?? '—' }}</p>
                    <p class="mb-2"><strong>آدرس:</strong> {{ $order->address }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>مبلغ:</strong> {{ number_format($order->total_amount) }} تومان</p>
                    <p class="mb-2"><strong>شماره پیگیری سفارش:</strong> {{ $order->tracking_code ?: '—' }}</p>
                    <p class="mb-0"><strong>تاریخ ثبت:</strong> {{ app(\App\Support\DateFormatter::class)->format($order->created_at) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header">اقلام سفارش</div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>محصول</th>
                        <th>تعداد</th>
                        <th>قیمت واحد</th>
                        <th>جمع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->localized_name ?? 'محصول حذف‌شده' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price) }} تومان</td>
                            <td>{{ number_format($item->total) }} تومان</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($payment)
        <div class="card mb-4 shadow-sm">
            <div class="card-header">اطلاعات پرداخت</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div><strong>روش پرداخت:</strong> {{ $payment->method?->title ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div><strong>نوع روش:</strong> {{ $payment->method?->type ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div><strong>وضعیت پرداخت:</strong> {{ __('messages.payment_statuses.' . $payment->status) }}</div>
                    </div>
                    <div class="col-md-3">
                        <div><strong>مبلغ پرداخت:</strong> {{ number_format($payment->amount) }} تومان</div>
                    </div>
                </div>

                @if($isReceiptPayment && $isAwaitingReceipt)
                    <div class="alert alert-warning mt-3 mb-0">
                        این سفارش با روش رسید بانکی ثبت شده اما هنوز کاربر رسیدی بارگذاری نکرده است.
                    </div>
                @elseif($isReceiptPayment && $isUnderReview)
                    <div class="alert alert-info mt-3 mb-0">
                        رسید بانکی ثبت شده و این پرداخت در انتظار بررسی مدیر است.
                    </div>
                @elseif($isReceiptPayment && $payment->status === 'rejected' && $hasUploadedReceipt)
                    <div class="alert alert-danger mt-3 mb-0">
                        رسید قبلی رد شده است و مشتری باید رسید جدید بارگذاری کند.
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($isReceiptPayment)
        <div class="card mb-4 shadow-sm">
            <div class="card-header">رسید بانکی</div>
            <div class="card-body">
                @if($latestReceipt)
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold mb-3">اطلاعات رسید</div>

                                <div class="mb-2"><strong>وضعیت رسید:</strong> {{ $latestReceipt->status }}</div>
                                <div class="mb-2"><strong>وضعیت پرداخت:</strong> {{ __('messages.payment_statuses.' . $payment->status) }}</div>
                                <div class="mb-2"><strong>شماره پیگیری بانکی:</strong> {{ $latestReceipt->tracking_number ?: '—' }}</div>
                                <div class="mb-2"><strong>ثبت‌کننده:</strong> {{ $latestReceipt->uploader?->name ?? $latestReceipt->uploader?->email ?? '—' }}</div>
                                <div class="mb-2"><strong>تاریخ ثبت:</strong> {{ app(\App\Support\DateFormatter::class)->format($latestReceipt->created_at) }}</div>

                                @if($latestReceipt->uploaded_at)
                                    <div class="mb-2"><strong>زمان آپلود:</strong> {{ app(\App\Support\DateFormatter::class)->format($latestReceipt->uploaded_at) }}</div>
                                @endif

                                @if($latestReceipt->note)
                                    <div class="mt-3">
                                        <strong>توضیحات کاربر:</strong>
                                        <div class="border rounded p-2 bg-light mt-2">{{ $latestReceipt->note }}</div>
                                    </div>
                                @endif

                                @if($latestReceipt->rejection_reason)
                                    <div class="alert alert-danger mt-3 mb-0">
                                        <strong>دلیل رد:</strong> {{ $latestReceipt->rejection_reason }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold mb-3">فایل رسید</div>

                                @if($latestReceipt->file_url)
                                    @if($latestReceipt->is_image)
                                        <img
                                            src="{{ $latestReceipt->file_url }}"
                                            alt="رسید بانکی"
                                            class="img-fluid rounded border mb-3"
                                        >
                                    @else
                                        <iframe
                                            src="{{ $latestReceipt->file_url }}"
                                            style="width:100%;height:320px;border:1px solid #dee2e6;border-radius:.5rem;"
                                        ></iframe>
                                    @endif

                                    <div class="d-flex gap-2 flex-wrap mt-3">
                                        <a href="{{ $latestReceipt->file_url }}" target="_blank" class="btn btn-outline-secondary">
                                            مشاهده فایل
                                        </a>
                                        <a href="{{ route('admin.bank-receipts.show', $latestReceipt) }}" class="btn {{ $canReviewReceipt ? 'btn-outline-danger' : 'btn-outline-primary' }}">
                                            {{ $canReviewReceipt ? 'بررسی رسید' : 'مشاهده رسید' }}
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-3">فایلی برای این رسید بارگذاری نشده است.</div>

                                    <a href="{{ route('admin.bank-receipts.show', $latestReceipt) }}" class="btn {{ $canReviewReceipt ? 'btn-outline-danger' : 'btn-outline-primary' }}">
                                        {{ $canReviewReceipt ? 'بررسی رسید' : 'مشاهده رسید' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        هنوز رسیدی برای این سفارش ثبت نشده است.
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">وضعیت ارسال و پیگیری</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="status" class="form-label">وضعیت سفارش</label>
                    <select id="status" name="status" class="form-select" required>
                        @foreach(['pending', 'paid', 'canceled', 'shipped', 'delivered'] as $value)
                            <option value="{{ $value }}" @selected($order->status === $value)>{{ __('messages.order_statuses.' . $value) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="payment_status" class="form-label">وضعیت پرداخت</label>
                    <select id="payment_status" name="payment_status" class="form-select" required>
                        @foreach(['pending', 'initiated', 'pending_review', 'paid', 'failed', 'rejected', 'expired'] as $value)
                            <option value="{{ $value }}" @selected(($payment?->status ?? 'pending') === $value)>{{ __('messages.payment_statuses.' . $value) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tracking_code" class="form-label">شماره پیگیری سفارش</label>
                    <input
                        id="tracking_code"
                        name="tracking_code"
                        value="{{ old('tracking_code', $order->tracking_code) }}"
                        class="form-control"
                        maxlength="100"
                    >
                </div>

                <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
            </form>
        </div>
    </div>
@endsection