@extends('themes.admin.layouts.master')

@section('title', 'ویرایش سفارش')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>سفارش #{{ $order->id }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>مشتری:</strong> {{ $order->user->name ?? $order->user->email ?? '—' }}</p>
            <p><strong>ایمیل:</strong> {{ $order->user->email ?? '—' }}</p>
            <p><strong>آدرس:</strong> {{ $order->address }}</p>
            <p><strong>مبلغ:</strong> {{ number_format($order->total_amount) }} تومان</p>
        </div>
    </div>

    <div class="card mb-4">
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
                            <td>{{ $item->product->name ?? 'محصول حذف‌شده' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price) }} تومان</td>
                            <td>{{ number_format($item->total) }} تومان</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">وضعیت ارسال و پیگیری</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="status" class="form-label">وضعیت سفارش</label>
                    <select id="status" name="status" class="form-select" required>
                        @foreach(['pending' => 'در انتظار', 'paid' => 'پرداخت‌شده', 'canceled' => 'لغوشده', 'shipped' => 'ارسال‌شده', 'delivered' => 'تحویل‌شده'] as $value => $label)
                            <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tracking_code" class="form-label">شماره پیگیری</label>
                    <input id="tracking_code" name="tracking_code" value="{{ old('tracking_code', $order->tracking_code) }}" class="form-control" maxlength="100">
                </div>
                <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
            </form>
        </div>
    </div>
@endsection
