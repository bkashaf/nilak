@extends('themes.admin.layouts.master')

@section('title', 'مدیریت سفارش‌ها')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">مدیریت سفارش‌ها</h1>
            <p class="text-muted mb-0">بررسی سفارش‌ها، وضعیت پرداخت و رسیدهای بانکی مشتریان</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>شماره</th>
                    <th>مشتری</th>
                    <th>مبلغ</th>
                    <th>وضعیت سفارش</th>
                    <th>روش پرداخت</th>
                    <th>وضعیت پرداخت</th>
                    <th>رسید بانکی</th>
                    <th>شماره پیگیری</th>
                    <th>تاریخ</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php($payment = $order->payments->sortByDesc('id')->first())
                    @php($latestReceipt = $payment?->latestBankReceipt)
                    @php($isReceiptPayment = $payment && in_array($payment->method?->type, ['receipt'], true))
                    @php($isReceiptPayment = $isReceiptPayment || ($payment && $payment->method?->name === 'bank_receipt'))

                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->name ?? $order->user->email ?? '—' }}</td>
                        <td>{{ number_format($order->total_amount) }} تومان</td>
                        <td>{{ __('messages.order_statuses.' . $order->status) }}</td>
                        <td>{{ $payment?->method?->title ?? '—' }}</td>
                        <td>{{ $payment ? __('messages.payment_statuses.' . $payment->status) : '—' }}</td>
                        <td>
                            @if($isReceiptPayment)
                                @if($latestReceipt)
                                    <div class="small">
                                        <div><strong>وضعیت:</strong> {{ $latestReceipt->status }}</div>
                                        <div><strong>پیگیری:</strong> {{ $latestReceipt->tracking_number ?: '—' }}</div>
                                    </div>
                                @elseif(in_array($payment?->status, ['pending', 'initiated'], true))
                                    <span class="badge bg-warning text-dark">در انتظار ارسال رسید</span>
                                @else
                                    <span class="text-muted">هنوز رسیدی ثبت نشده</span>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $order->tracking_code ?? '—' }}</td>
                        <td>{{ app(\App\Support\DateFormatter::class)->format($order->created_at) }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-primary">
                                    مشاهده و ویرایش
                                </a>

                                @if($isReceiptPayment && $latestReceipt)
                                    <a href="{{ route('admin.bank-receipts.show', $latestReceipt) }}" class="btn btn-sm btn-outline-danger">
                                        بررسی رسید
                                    </a>
                                @elseif($isReceiptPayment)
                                    <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary">
                                        بررسی پرداخت
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">هنوز سفارشی ثبت نشده است.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $orders->links() }}
@endsection