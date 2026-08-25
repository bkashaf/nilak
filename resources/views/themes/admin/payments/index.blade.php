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
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr><th>شناسه</th><th>سفارش</th><th>مشتری</th><th>روش پرداخت</th><th>مبلغ</th><th>وضعیت</th><th>تاریخ‌ها</th><th>رسید</th><th>عملیات</th></tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        @php($receipt = $payment->bankReceipts->sortByDesc('id')->first())
                        <tr>
                            <td>#{{ $payment->id }}</td>
                            <td>{{ $payment->order ? '#' . $payment->order->id : '—' }}</td>
                            <td>{{ $payment->order?->user?->name ?? $payment->order?->user?->email ?? '—' }}</td>
                            <td>{{ $payment->method?->title ?? '—' }}</td>
                            <td>{{ number_format($payment->amount) }} تومان</td>
                            <td>{{ __('messages.payment_statuses.' . $payment->status) }}</td>
                            <td class="small text-muted">
                                <div>ثبت: {{ app(\App\Support\DateFormatter::class)->format($payment->created_at) }}</div>
                                <div>آخرین تغییر: {{ app(\App\Support\DateFormatter::class)->format($payment->updated_at) }}</div>
                                @if($payment->paid_at)
                                    <div>پرداخت: {{ app(\App\Support\DateFormatter::class)->format($payment->paid_at) }}</div>
                                @endif
                            </td>
                            <td>
                                @if($receipt)
                                    <a href="{{ asset('storage/' . $receipt->file_path) }}" target="_blank">مشاهده رسید</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.payments.update', $payment) }}" class="d-flex gap-1 mb-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm" aria-label="وضعیت پرداخت">
                                        @foreach(['pending', 'initiated', 'pending_review', 'paid', 'failed', 'rejected', 'expired'] as $status)
                                            <option value="{{ $status }}" @selected($payment->status === $status)>{{ __('messages.payment_statuses.' . $status) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-primary text-nowrap">ذخیره</button>
                                </form>
                                @if($payment->status === 'paid')
                                    <form method="POST" action="{{ route('admin.payments.refund', $payment) }}" class="d-flex gap-1">
                                        @csrf
                                        <input name="amount" type="number" min="1" max="{{ $payment->amount }}" class="form-control form-control-sm" placeholder="مبلغ بازپرداخت" required>
                                        <input name="reason" class="form-control form-control-sm" placeholder="دلیل">
                                        <button class="btn btn-sm btn-outline-danger text-nowrap">بازپرداخت</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-4">پرداختی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $payments->links() }}</div>
@endsection
