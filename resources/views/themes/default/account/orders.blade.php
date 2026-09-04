@extends('themes.default.layouts.shop')

@section('title', 'سفارش‌های من')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-4">سفارش‌های من</h1>

    @if($orders->isEmpty())
        <div class="alert alert-info">هنوز سفارشی ثبت نکرده‌اید.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th>شماره سفارش</th>
                        <th>شماره پیگیری</th>
                        <th>تاریخ</th>
                        <th>مبلغ</th>
                        <th>روش پرداخت</th>
                        <th>وضعیت پرداخت</th>
                        <th>وضعیت رسید</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        @php($payment = $order->payments->sortByDesc('id')->first())
                        @php($latestReceipt = $payment?->latestBankReceipt)
                        @php($isReceiptPayment = $payment?->isReceiptPayment() ?? false)
                        @php($canUploadReceipt = $payment?->canUploadReceipt() ?? false)
                        @php($isAwaitingReceipt = $payment?->isAwaitingReceipt() ?? false)
                        @php($isUnderReceiptReview = $payment?->isUnderReceiptReview() ?? false)
                        @php($hasUploadedReceipt = $payment?->hasUploadedReceipt() ?? false)

                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>
                                <span class="badge bg-secondary p-2">{{ $order->tracking_code }}</span>
                            </td>
                            <td>{{ jdate($order->created_at)->format('Y/m/d') }}</td>
                            <td>{{ number_format($order->total_amount) }} تومان</td>
                            <td>{{ $payment?->method?->title ?? 'ثبت نشده' }}</td>
                            <td>{{ $payment ? __('messages.payment_statuses.' . $payment->status) : 'ثبت نشده' }}</td>
                            <td>
                                @if($isReceiptPayment && $payment)
                                    @if($isAwaitingReceipt)
                                        <span class="badge bg-warning text-dark">در انتظار ارسال رسید</span>
                                    @elseif($isUnderReceiptReview)
                                        <div class="small">
                                            <div><span class="badge bg-info text-dark">در انتظار بررسی</span></div>
                                            <div class="mt-1">{{ $latestReceipt?->tracking_number ?: 'بدون شماره پیگیری' }}</div>
                                        </div>
                                    @elseif($payment->status === 'paid' && $hasUploadedReceipt)
                                        <div class="small">
                                            <div><span class="badge bg-success">تایید شده</span></div>
                                            <div class="mt-1">{{ $latestReceipt?->tracking_number ?: 'بدون شماره پیگیری' }}</div>
                                        </div>
                                    @elseif($payment->status === 'rejected' && $hasUploadedReceipt)
                                        <div class="small">
                                            <div><span class="badge bg-danger">رد شده</span></div>
                                            <div class="mt-1">{{ $latestReceipt?->tracking_number ?: 'بدون شماره پیگیری' }}</div>
                                        </div>
                                    @elseif($hasUploadedReceipt)
                                        <div class="small">
                                            <div><strong>{{ $latestReceipt?->status ?? 'ثبت شده' }}</strong></div>
                                            <div>{{ $latestReceipt?->tracking_number ?: 'بدون شماره پیگیری' }}</div>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                        مشاهده جزئیات
                                    </a>

                                    @if($isReceiptPayment && $canUploadReceipt)
                                        <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-outline-danger">
                                            {{ $payment->status === 'rejected' ? 'ارسال مجدد رسید' : 'ارسال رسید بانکی' }}
                                        </a>
                                    @elseif($isReceiptPayment && $hasUploadedReceipt)
                                        <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                                            مشاهده رسید
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    @endif
</div>
@endsection