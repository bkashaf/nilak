@extends('themes.admin.layouts.master')

@section('title', 'بررسی رسید بانکی')

@section('content')
    @php($payment = $bankReceipt->payment)
    @php($order = $payment?->order)
    @php($latestReceipt = $payment?->latestBankReceipt)
    @php($isReceiptPayment = $payment?->isReceiptPayment() ?? false)
    @php($isUnderReview = $payment?->isUnderReceiptReview() ?? false)
    @php($isLatestReceipt = $latestReceipt && $latestReceipt->id === $bankReceipt->id)
    @php($canReviewReceipt = $isReceiptPayment && $isUnderReview && $isLatestReceipt && $bankReceipt->status === 'pending_review')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">رسید بانکی #{{ $bankReceipt->id }}</h1>
            <p class="text-muted mb-0">بررسی رسید ارسال‌شده توسط مشتری و تعیین وضعیت نهایی پرداخت</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($order)
                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-secondary">
                    بازگشت به سفارش
                </a>
            @endif

            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                بازگشت به پرداخت‌ها
            </a>
        </div>
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

    @if(! $payment)
        <div class="alert alert-danger">
            برای این رسید، پرداخت معتبری پیدا نشد.
        </div>
    @elseif(! $isReceiptPayment)
        <div class="alert alert-danger">
            پرداخت مرتبط با این رکورد از نوع رسید بانکی نیست.
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header">پیش‌نمایش رسید</div>
                <div class="card-body text-center">
                    @if($bankReceipt->file_url)
                        @if($bankReceipt->is_image)
                            <img
                                src="{{ $bankReceipt->file_url }}"
                                alt="رسید بانکی"
                                class="img-fluid rounded border"
                            >
                        @else
                            <iframe
                                src="{{ $bankReceipt->file_url }}"
                                style="width:100%;height:620px;border:1px solid #dee2e6;border-radius:.5rem;"
                            ></iframe>
                        @endif

                        <div class="mt-3 d-flex gap-2 justify-content-center flex-wrap">
                            <a href="{{ $bankReceipt->file_url }}" target="_blank" class="btn btn-outline-primary">
                                باز کردن فایل
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            فایلی برای این رسید بارگذاری نشده است.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header">اطلاعات رسید و پرداخت</div>
                <div class="card-body">
                    <p><strong>سفارش:</strong> #{{ $order?->id ?? '—' }}</p>
                    <p><strong>شماره پیگیری سفارش:</strong> {{ $order?->tracking_code ?? '—' }}</p>
                    <p><strong>مشتری:</strong> {{ $order?->user?->name ?? $order?->user?->email ?? '—' }}</p>
                    <p><strong>روش پرداخت:</strong> {{ $payment?->method?->title ?? '—' }}</p>
                    <p><strong>وضعیت رسید:</strong> {{ $bankReceipt->status }}</p>
                    <p><strong>وضعیت پرداخت:</strong> {{ $payment ? __('messages.payment_statuses.' . $payment->status) : '—' }}</p>
                    <p><strong>شماره پیگیری بانکی:</strong> {{ $bankReceipt->tracking_number ?: '—' }}</p>
                    <p><strong>ثبت‌کننده:</strong> {{ $bankReceipt->uploader?->name ?? $bankReceipt->uploader?->email ?? '—' }}</p>
                    <p><strong>زمان آپلود:</strong> {{ $bankReceipt->uploaded_at ? app(\App\Support\DateFormatter::class)->format($bankReceipt->uploaded_at) : '—' }}</p>

                    @if($bankReceipt->reviewer)
                        <p><strong>بررسی‌کننده:</strong> {{ $bankReceipt->reviewer->name ?? $bankReceipt->reviewer->email ?? '—' }}</p>
                    @endif

                    @if($bankReceipt->reviewed_at)
                        <p><strong>زمان بررسی:</strong> {{ app(\App\Support\DateFormatter::class)->format($bankReceipt->reviewed_at) }}</p>
                    @endif

                    @if($bankReceipt->note)
                        <div class="border rounded p-3 bg-light mt-3">
                            <div class="fw-semibold mb-2">توضیحات کاربر</div>
                            <div>{{ $bankReceipt->note }}</div>
                        </div>
                    @endif

                    @if($bankReceipt->rejection_reason)
                        <div class="alert alert-danger mt-3 mb-0">
                            <strong>دلیل رد:</strong> {{ $bankReceipt->rejection_reason }}
                        </div>
                    @endif
                </div>
            </div>

            @if($payment && $isReceiptPayment)
                @if($canReviewReceipt)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">تأیید رسید</div>
                        <div class="card-body">
                            <p class="text-muted small">
                                با تأیید این رسید، وضعیت پرداخت به «پرداخت‌شده» تغییر می‌کند و وضعیت سفارش نیز طبق منطق مرکزی پرداخت به‌روزرسانی می‌شود.
                            </p>

                            <form method="POST" action="{{ route('admin.bank-receipts.approve', $bankReceipt) }}">
                                @csrf
                                <button class="btn btn-success w-100">تأیید رسید و ثبت پرداخت</button>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header">رد رسید</div>
                        <div class="card-body">
                            <p class="text-muted small">
                                در صورت رد این رسید، وضعیت پرداخت به «ردشده» تغییر می‌کند و مشتری باید رسید جدید بارگذاری کند.
                            </p>

                            <form method="POST" action="{{ route('admin.bank-receipts.reject', $bankReceipt) }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">دلیل رد</label>
                                    <textarea name="rejection_reason" class="form-control" rows="4" required>{{ old('rejection_reason') }}</textarea>
                                </div>

                                <button class="btn btn-danger w-100">رد رسید</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm">
                        <div class="card-header">وضعیت بررسی</div>
                        <div class="card-body">
                            @if($bankReceipt->status === 'approved')
                                <div class="alert alert-success mb-0">
                                    این رسید قبلاً تأیید شده است.
                                </div>
                            @elseif($bankReceipt->status === 'rejected')
                                <div class="alert alert-danger mb-0">
                                    این رسید قبلاً رد شده است.
                                </div>
                            @elseif(! $isUnderReview)
                                <div class="alert alert-warning mb-0">
                                    پرداخت مرتبط با این رسید اکنون در وضعیت قابل بررسی نیست.
                                </div>
                            @elseif(! $isLatestReceipt)
                                <div class="alert alert-warning mb-0">
                                    این رسید آخرین رسید ثبت‌شده برای این پرداخت نیست و فقط برای مشاهده نگه داشته می‌شود.
                                </div>
                            @else
                                <div class="alert alert-secondary mb-0">
                                    برای این رسید در حال حاضر اقدامی در دسترس نیست.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection