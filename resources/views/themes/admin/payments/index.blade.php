@extends('themes.admin.layouts.master')

@section('title', 'مدیریت پرداخت‌ها')

@section('content')
    <div class="mb-4">
        <h1 class="h2 mb-1">مدیریت پرداخت‌ها</h1>
        <p class="text-muted mb-0">بررسی پرداخت‌های آنلاین، در محل و رسیدهای بانکی مشتریان</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>سفارش</th>
                        <th>مشتری</th>
                        <th>روش پرداخت</th>
                        <th>مبلغ</th>
                        <th>وضعیت پرداخت</th>
                        <th>رسید بانکی</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        @php($receipt = $payment->latestBankReceipt)
                        @php($isReceiptPayment = $payment->isReceiptPayment())
                        @php($isAwaitingReceipt = $payment->isAwaitingReceipt())
                        @php($isUnderReview = $payment->isUnderReceiptReview())
                        @php($hasUploadedReceipt = $payment->hasUploadedReceipt())
                        @php($canReviewReceipt = $isReceiptPayment && $isUnderReview && $receipt && $receipt->status === 'pending_review')

                        <tr>
                            <td>#{{ $payment->id }}</td>
                            <td>
                                @if($payment->order)
                                    <div>#{{ $payment->order->id }}</div>
                                    <div class="small text-muted">{{ $payment->order->tracking_code }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $payment->order?->user?->name ?? $payment->order?->user?->email ?? '—' }}</td>
                            <td>
                                <div>{{ $payment->method?->title ?? '—' }}</div>
                                <div class="small text-muted">{{ $payment->method?->type ?? '—' }}</div>
                            </td>
                            <td>{{ number_format($payment->amount) }} تومان</td>
                            <td>{{ __('messages.payment_statuses.' . $payment->status) }}</td>
                            <td>
                                @if($isReceiptPayment)
                                    @if($isAwaitingReceipt)
                                        <span class="badge bg-warning text-dark">در انتظار ارسال رسید توسط مشتری</span>
                                    @elseif($isUnderReview && $receipt)
                                        <div class="small">
                                            <div><span class="badge bg-info text-dark">در انتظار بررسی مدیر</span></div>
                                            <div class="mt-1"><strong>شماره پیگیری:</strong> {{ $receipt->tracking_number ?: '—' }}</div>
                                            <div><strong>تاریخ ثبت:</strong> {{ app(\App\Support\DateFormatter::class)->format($receipt->created_at) }}</div>
                                        </div>
                                    @elseif($payment->status === 'paid' && $hasUploadedReceipt)
                                        <div class="small">
                                            <div><span class="badge bg-success">تأیید شده</span></div>
                                            <div class="mt-1"><strong>شماره پیگیری:</strong> {{ $receipt?->tracking_number ?: '—' }}</div>
                                            <div><strong>تاریخ ثبت:</strong> {{ $receipt ? app(\App\Support\DateFormatter::class)->format($receipt->created_at) : '—' }}</div>
                                        </div>
                                    @elseif($payment->status === 'rejected' && $hasUploadedReceipt)
                                        <div class="small">
                                            <div><span class="badge bg-danger">رد شده</span></div>
                                            <div class="mt-1"><strong>شماره پیگیری:</strong> {{ $receipt?->tracking_number ?: '—' }}</div>
                                            @if($receipt?->rejection_reason)
                                                <div class="text-danger mt-1">{{ $receipt->rejection_reason }}</div>
                                            @endif
                                        </div>
                                    @elseif($hasUploadedReceipt && $receipt)
                                        <div class="small">
                                            <div><strong>وضعیت رسید:</strong> {{ $receipt->status }}</div>
                                            <div><strong>شماره پیگیری:</strong> {{ $receipt->tracking_number ?: '—' }}</div>
                                            <div><strong>تاریخ ثبت:</strong> {{ app(\App\Support\DateFormatter::class)->format($receipt->created_at) }}</div>
                                        </div>
                                    @else
                                        <span class="text-muted">هنوز رسیدی ثبت نشده است</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    <form method="POST" action="{{ route('admin.payments.update', $payment) }}" class="d-flex gap-1">
                                        @csrf
                                        @method('PUT')

                                        <select name="status" class="form-select form-select-sm" aria-label="وضعیت پرداخت">
                                            @foreach(['pending', 'initiated', 'pending_review', 'paid', 'failed', 'rejected', 'expired'] as $status)
                                                <option value="{{ $status }}" @selected($payment->status === $status)>{{ __('messages.payment_statuses.' . $status) }}</option>
                                            @endforeach
                                        </select>

                                        <button class="btn btn-sm btn-primary text-nowrap">ذخیره</button>
                                    </form>

                                    @if($payment->order)
                                        <a href="{{ route('admin.orders.edit', $payment->order) }}" class="btn btn-sm btn-outline-dark">
                                            مشاهده سفارش
                                        </a>
                                    @endif

                                    @if($isReceiptPayment && $receipt)
                                        <a href="{{ route('admin.bank-receipts.show', $receipt) }}" class="btn btn-sm {{ $canReviewReceipt ? 'btn-outline-danger' : 'btn-outline-secondary' }}">
                                            {{ $canReviewReceipt ? 'بررسی رسید' : 'مشاهده رسید' }}
                                        </a>

                                        @if($receipt->file_url)
                                            <a href="{{ $receipt->file_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                فایل رسید
                                            </a>
                                        @endif
                                    @endif

                                    @if($payment->status === 'paid')
                                        <form method="POST" action="{{ route('admin.payments.refund', $payment) }}" class="d-flex gap-1">
                                            @csrf

                                            <input
                                                name="amount"
                                                type="number"
                                                min="1"
                                                max="{{ $payment->amount }}"
                                                class="form-control form-control-sm"
                                                placeholder="مبلغ بازپرداخت"
                                                required
                                            >

                                            <input
                                                name="reason"
                                                class="form-control form-control-sm"
                                                placeholder="دلیل"
                                            >

                                            <button class="btn btn-sm btn-outline-danger text-nowrap">بازپرداخت</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">پرداختی ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $payments->links() }}</div>
@endsection