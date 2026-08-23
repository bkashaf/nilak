@extends('themes.admin.layouts.master')

@section('title', 'مدیریت سفارش‌ها')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>مدیریت سفارش‌ها</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>شماره</th>
                    <th>مشتری</th>
                    <th>مبلغ</th>
                    <th>وضعیت سفارش</th>
                    <th>پرداخت</th>
                    <th>شماره پیگیری</th>
                    <th>تاریخ</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php($payment = $order->payments->sortByDesc('id')->first())
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->name ?? $order->user->email ?? '—' }}</td>
                        <td>{{ number_format($order->total_amount) }} تومان</td>
                        <td>{{ __('messages.order_statuses.' . $order->status) }}</td>
                        <td>{{ $payment ? __('messages.payment_statuses.' . $payment->status) : '—' }}</td>
                        <td>{{ $order->tracking_code ?? '—' }}</td>
                        <td>{{ app(\App\Support\DateFormatter::class)->format($order->created_at) }}</td>
                        <td>
                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-primary">مشاهده و ویرایش</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">هنوز سفارشی ثبت نشده است.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $orders->links() }}
@endsection
