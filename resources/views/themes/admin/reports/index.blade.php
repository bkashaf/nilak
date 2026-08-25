@extends('themes.admin.layouts.master')

@section('title', 'گزارش‌های فروشگاه')

@section('content')
    <div class="mb-4">
        <h1 class="h2 mb-1">گزارش‌های فروشگاه</h1>
        <p class="text-muted mb-0">نمای کلی از کاربران، محصولات، سفارش‌ها و پرداخت‌ها</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3"><div class="card shadow-sm h-100"><div class="card-body"><div class="text-muted">کاربران</div><div class="fs-3 fw-bold">{{ number_format($stats['users']) }}</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card shadow-sm h-100"><div class="card-body"><div class="text-muted">محصولات</div><div class="fs-3 fw-bold">{{ number_format($stats['products']) }}</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card shadow-sm h-100"><div class="card-body"><div class="text-muted">سفارش‌ها</div><div class="fs-3 fw-bold">{{ number_format($stats['orders']) }}</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card shadow-sm h-100"><div class="card-body"><div class="text-muted">درآمد ثبت‌شده</div><div class="fs-3 fw-bold">{{ number_format($stats['sales']) }} تومان</div></div></div></div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">آخرین سفارش‌ها</div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead><tr><th>شماره</th><th>مشتری</th><th>مبلغ</th><th>وضعیت</th><th>تاریخ ثبت</th><th>آخرین به‌روزرسانی</th></tr></thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr><td>#{{ $order->id }}</td><td>{{ $order->user->name ?? $order->user->email ?? '—' }}</td><td>{{ number_format($order->total_amount) }} تومان</td><td>{{ $order->status }}</td><td>{{ app(\App\Support\DateFormatter::class)->format($order->created_at) }}</td><td>{{ app(\App\Support\DateFormatter::class)->format($order->updated_at) }}</td></tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">هنوز سفارشی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
