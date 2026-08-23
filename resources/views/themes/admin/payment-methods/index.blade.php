@extends('themes.admin.layouts.master')

@section('title', 'روش‌های پرداخت')

@section('content')
    <div class="mb-4">
        <h1 class="h2 mb-1">روش‌های پرداخت</h1>
        <p class="text-muted mb-0">روش‌های قابل انتخاب مشتری در checkout</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        @foreach($paymentMethods as $paymentMethod)
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 mb-0">{{ $paymentMethod->title }}</h2>
                            <span class="badge {{ $paymentMethod->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $paymentMethod->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('admin.payment-methods.update', $paymentMethod) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="title-{{ $paymentMethod->id }}" class="form-label">عنوان نمایشی</label>
                                <input id="title-{{ $paymentMethod->id }}" name="title" value="{{ $paymentMethod->title }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="type-{{ $paymentMethod->id }}" class="form-label">نوع</label>
                                <select id="type-{{ $paymentMethod->id }}" name="type" class="form-select">
                                    @foreach(['cod' => 'پرداخت در محل', 'receipt' => 'رسید بانکی', 'gateway' => 'درگاه آنلاین'] as $type => $label)
                                        <option value="{{ $type }}" @selected($paymentMethod->type === $type)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="config-{{ $paymentMethod->id }}" class="form-label">تنظیمات فنی JSON</label>
                                <textarea id="config-{{ $paymentMethod->id }}" name="config" class="form-control" rows="2" dir="ltr">{{ $paymentMethod->config ? json_encode($paymentMethod->config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '' }}</textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input id="active-{{ $paymentMethod->id }}" name="is_active" value="1" type="checkbox" class="form-check-input" @checked($paymentMethod->is_active)>
                                <label for="active-{{ $paymentMethod->id }}" class="form-check-label">این روش برای مشتری فعال باشد</label>
                            </div>
                            <button class="btn btn-primary">ذخیره روش پرداخت</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
