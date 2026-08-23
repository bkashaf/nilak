@extends('themes.admin.layouts.master')

@section('title', 'مدیریت پرداخت‌ها')

@section('content')
    <div class="mb-4">
        <h1 class="h2 mb-1">مدیریت پرداخت‌ها</h1>
        <p class="text-muted mb-0">بررسی پرداخت‌های آنلاین، در محل و رسیدهای بانکی</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr><th>شناسه</th><th>سفارش</th><th>مشتری</th><th>روش پرداخت</th><th>مبلغ</th><th>وضعیت</th><th>رسید</th><th>عملیات</th></tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        @php($receiptPath = data_get($payment->callback_data, 'receipt_path'))
                        <tr>
                            <td>#{{ $payment->id }}</td>
                            <td>{{ $payment->order ? '#' . $payment->order->id : '—' }}</td>
                            <td>{{ $payment->order?->user?->name ?? $payment->order?->user?->email ?? '—' }}</td>
                            <td>{{ $payment->method?->title ?? '—' }}</td>
                            <td>{{ number_format($payment->amount) }} تومان</td>
                            <td>{{ __('messages.payment_statuses.' . $payment->status) }}</td>
                            <td>
                                @if($receiptPath)
                                    <a href="{{ asset('storage/' . $receiptPath) }}" target="_blank">مشاهده رسید</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.payments.update', $payment) }}" class="d-flex gap-1">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm" aria-label="وضعیت پرداخت">
                                        @foreach(['pending', 'initiated', 'pending_review', 'paid', 'failed', 'rejected'] as $status)
                                            <option value="{{ $status }}" @selected($payment->status === $status)>{{ __('messages.payment_statuses.' . $status) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-primary text-nowrap">ذخیره</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4">پرداختی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $payments->links() }}</div>
@endsection
