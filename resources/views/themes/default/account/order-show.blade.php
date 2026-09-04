@extends('themes.default.layouts.shop')

@section('title', 'جزئیات سفارش')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-4">جزئیات سفارش شماره {{ $order->id }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @php($payment = $order->payments->sortByDesc('id')->first())
    @php($latestReceipt = $payment?->bankReceipts->sortByDesc('id')->first())
    @php($isReceiptPayment = $payment && in_array($payment->method?->type, ['receipt'], true))
    @php($isReceiptPayment = $isReceiptPayment || ($payment && $payment->method?->name === 'bank_receipt'))
    @php($canUploadReceipt = $payment && in_array($payment->status, ['pending', 'initiated', 'rejected'], true))

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <p><strong>تاریخ ثبت:</strong> {{ jdate($order->created_at)->format('Y/m/d') }}</p>
            <p><strong>مبلغ کل:</strong> {{ number_format($order->total_amount) }} تومان</p>
            <p><strong>شماره پیگیری سفارش:</strong> <span class="badge bg-secondary p-2">{{ $order->tracking_code }}</span></p>

            @if($payment)
                <p><strong>روش پرداخت:</strong> {{ $payment->method?->title ?? $payment->method?->type ?? '—' }}</p>
                <p><strong>وضعیت پرداخت:</strong> {{ __('messages.payment_statuses.' . $payment->status) }}</p>
            @else
                <p><strong>وضعیت پرداخت:</strong> ثبت نشده</p>
            @endif
        </div>
    </div>

    @if($isReceiptPayment)
        <div class="card mb-4 shadow-sm">
            <div class="card-header">پرداخت با رسید بانکی</div>
            <div class="card-body">
                @if($payment->status === 'pending_review')
                    <div class="alert alert-info mb-3">
                        رسید شما ثبت شده و در انتظار بررسی مدیر است.
                    </div>
                @endif

                @if($payment->status === 'paid')
                    <div class="alert alert-success mb-3">
                        این پرداخت توسط مدیر تایید شده است.
                    </div>
                @endif

                @if($payment->status === 'rejected' && $latestReceipt?->rejection_reason)
                    <div class="alert alert-danger mb-3">
                        رسید قبلی رد شده است: {{ $latestReceipt->rejection_reason }}
                    </div>
                @endif

                @if($latestReceipt)
                    <div class="border rounded p-3 bg-light mb-4">
                        <div class="fw-semibold mb-3">آخرین رسید ثبت‌شده</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div><strong>وضعیت رسید:</strong> {{ $latestReceipt->status }}</div>
                            </div>

                            <div class="col-md-6">
                                <div><strong>شماره پیگیری بانکی:</strong> {{ $latestReceipt->tracking_number ?: '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <div><strong>تاریخ ثبت:</strong> {{ jdate($latestReceipt->created_at)->format('Y/m/d H:i') }}</div>
                            </div>

                            <div class="col-md-6">
                                <div><strong>فایل:</strong>
                                    @if($latestReceipt->file_url)
                                        <a href="{{ $latestReceipt->file_url }}" target="_blank">مشاهده فایل رسید</a>
                                    @else
                                        <span>—</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($latestReceipt->note)
                            <div class="mt-3">
                                <strong>توضیحات:</strong>
                                <div class="mt-2">{{ $latestReceipt->note }}</div>
                            </div>
                        @endif

                        @if($latestReceipt->rejection_reason)
                            <div class="alert alert-danger mt-3 mb-0">
                                <strong>دلیل رد:</strong> {{ $latestReceipt->rejection_reason }}
                            </div>
                        @endif
                    </div>
                @endif

                @if($canUploadReceipt)
                    <div class="border rounded p-3">
                        <h2 class="h6 mb-3">
                            {{ $payment->status === 'rejected' ? 'ارسال مجدد رسید بانکی' : 'بارگذاری رسید بانکی' }}
                        </h2>

                        <form action="{{ route('account.receipt.upload', $payment->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">شماره پیگیری بانکی</label>
                                <input
                                    type="text"
                                    name="tracking_number"
                                    class="form-control"
                                    value="{{ old('tracking_number', $latestReceipt?->tracking_number) }}"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">تصویر یا PDF رسید</label>
                                <input
                                    type="file"
                                    name="receipt"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                >
                                <div class="form-text">فایل رسید برای ثبت پرداخت الزامی است.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">توضیحات</label>
                                <textarea name="note" class="form-control" rows="3">{{ old('note', $payment->status === 'rejected' ? $latestReceipt?->note : '') }}</textarea>
                            </div>

                            <button class="btn btn-danger">
                                {{ $payment->status === 'rejected' ? 'ارسال مجدد رسید' : 'ثبت رسید بانکی' }}
                            </button>
                        </form>
                    </div>
                @elseif($payment->status !== 'pending_review' && $payment->status !== 'paid')
                    <div class="alert alert-warning mb-0">
                        این پرداخت فعلاً در وضعیتی نیست که امکان ثبت رسید برای آن نمایش داده شود.
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-header">اقلام سفارش</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>محصول</th>
                        <th>تعداد</th>
                        <th>قیمت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product?->localized_name ?? 'محصول حذف‌شده' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price) }} تومان</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($payment && $payment->bankReceipts->isNotEmpty())
        <div class="card mb-4 shadow-sm">
            <div class="card-header">رسیدهای ارسال‌شده</div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($payment->bankReceipts->sortByDesc('id') as $receipt)
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                    <div>
                                        <div><strong>وضعیت:</strong> {{ $receipt->status }}</div>
                                        <div><strong>شماره پیگیری بانکی:</strong> {{ $receipt->tracking_number ?: '—' }}</div>
                                        <div><strong>تاریخ:</strong> {{ jdate($receipt->created_at)->format('Y/m/d H:i') }}</div>
                                    </div>

                                    @if($receipt->file_url)
                                        <a href="{{ $receipt->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            مشاهده فایل رسید
                                        </a>
                                    @endif
                                </div>

                                @if($receipt->note)
                                    <div class="mb-2"><strong>توضیحات:</strong> {{ $receipt->note }}</div>
                                @endif

                                @if($receipt->rejection_reason)
                                    <div class="text-danger"><strong>دلیل رد:</strong> {{ $receipt->rejection_reason }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection