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
                        <th>وضعیت پرداخت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        @php($payment = $order->payments->sortByDesc('id')->first())
                        <tr>
                            <td>#{{ $order->id }}</td>

                            {{-- ⭐ شماره پیگیری + دکمه کپی + Badge --}}
                            <td>
                                <span class="badge bg-secondary p-2">{{ $order->tracking_code }}</span>

                                <button class="btn btn-outline-dark btn-sm ms-2"
                                        onclick="copyTracking('{{ $order->tracking_code }}')">
                                    📋
                                </button>
                            </td>

                            <td>{{ jdate($order->created_at)->format('Y/m/d') }}</td>
                            <td>{{ number_format($order->total_amount) }} تومان</td>
                            <td>{{ $payment?->status ?? 'ثبت نشده' }}</td>

                            <td>
                                <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                    مشاهده جزئیات
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    @endif

</div>

{{-- ⭐ Toast پیام کپی --}}
<div id="copyToast"
     style="position: fixed; bottom: 20px; right: 20px; background: #333; color: #fff;
            padding: 10px 20px; border-radius: 8px; display: none; z-index: 9999;">
    کپی شد!
</div>

<script>
function copyTracking(code) {
    // تلاش با API جدید
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(showCopyToast).catch(function () {
            fallbackCopy(code);
        });
    } else {
        // مرورگر قدیمی
        fallbackCopy(code);
    }
}

function fallbackCopy(code) {
    const tempInput = document.createElement('input');
    tempInput.value = code;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    showCopyToast();
}

function showCopyToast() {
    const toast = document.getElementById('copyToast');
    toast.style.display = 'block';
    setTimeout(() => {
        toast.style.display = 'none';
    }, 1500);
}
</script>

@endsection
