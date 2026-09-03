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

    {{-- اطلاعات کلی سفارش --}}
    <div class="card mb-4">
        <div class="card-body">

            <p><strong>تاریخ ثبت:</strong> {{ jdate($order->created_at)->format('Y/m/d') }}</p>

            <p><strong>مبلغ کل:</strong> {{ number_format($order->total_amount) }} تومان</p>

            {{-- ⭐ شماره پیگیری + Badge + دکمه کپی --}}
            <p>
                <strong>شماره پیگیری سفارش:</strong>

                <span class="badge bg-secondary p-2">{{ $order->tracking_code }}</span>

                <button class="btn btn-outline-dark btn-sm ms-2"
                        onclick="copyTracking('{{ $order->tracking_code }}')">
                    📋
                </button>
            </p>

            @php($payment = $order->payments->sortByDesc('id')->first())

            @if($payment)
                <p><strong>روش پرداخت:</strong> {{ $payment->method?->type }}</p>
                <p><strong>وضعیت پرداخت:</strong> {{ $payment->status }}</p>
            @else
                <p><strong>وضعیت پرداخت:</strong> ثبت نشده</p>
            @endif
        </div>
    </div>

    {{-- اقلام سفارش --}}
    <div class="card mb-4">
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
                            <td>{{ $item->product?->title ?? 'محصول حذف‌شده' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price) }} تومان</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- فرم ارسال رسید بانکی --}}
    @if($payment && $payment->method?->type === 'receipt' && $payment->status === 'initiated')
        <div class="card mb-4">
            <div class="card-header">ارسال رسید بانکی</div>
            <div class="card-body">

                <form action="{{ route('account.receipt.upload', $payment->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <label class="form-label">شماره پیگیری بانکی</label>
                    <input type="text" name="tracking_number" class="form-control" required>

                    <label class="form-label mt-3">تصویر رسید (اختیاری)</label>
                    <input type="file" name="receipt" class="form-control">

                    <label class="form-label mt-3">توضیحات (اختیاری)</label>
                    <textarea name="note" class="form-control"></textarea>

                    <button class="btn btn-primary mt-3">ارسال رسید</button>
                </form>

            </div>
        </div>
    @endif

    {{-- نمایش رسیدهای قبلی --}}
    @if($payment && $payment->bankReceipts->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header">رسیدهای ارسال‌شده</div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($payment->bankReceipts as $receipt)
                        <li class="list-group-item">
                            <strong>وضعیت:</strong> {{ $receipt->status }}  
                            <br>
                            <strong>تاریخ:</strong> {{ jdate($receipt->created_at)->format('Y/m/d H:i') }}
                            @if($receipt->rejection_reason)
                                <br>
                                <strong>دلیل رد:</strong> {{ $receipt->rejection_reason }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
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
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(showCopyToast).catch(function () {
            fallbackCopy(code);
        });
    } else {
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
