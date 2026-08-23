@extends('themes.admin.layouts.master')

@section('title', 'تنظیمات فروشگاه')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">تنظیمات فروشگاه</h1>
            <p class="text-muted mb-0">تنظیمات عمومی تجربه خرید و سفارش‌ها</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="row g-4">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <label for="store_name" class="form-label">نام فروشگاه</label>
                    <input id="store_name" name="store_name" value="{{ old('store_name', $settings['store_name']) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="currency_label" class="form-label">واحد نمایش قیمت</label>
                    <input id="currency_label" name="currency_label" value="{{ old('currency_label', $settings['currency_label']) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="default_locale" class="form-label">زبان پیش‌فرض</label>
                    <select id="default_locale" name="default_locale" class="form-select" required>
                        <option value="fa" @selected(old('default_locale', $settings['default_locale']) === 'fa')>فارسی</option>
                        <option value="en" @selected(old('default_locale', $settings['default_locale']) === 'en')>English</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="tracking_prefix" class="form-label">پیشوند شماره پیگیری</label>
                    <input id="tracking_prefix" name="tracking_prefix" value="{{ old('tracking_prefix', $settings['tracking_prefix']) }}" class="form-control" maxlength="10" required>
                    <div class="form-text">فقط حروف انگلیسی و عدد، مانند NLK</div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">ذخیره تنظیمات</button>
                </div>
            </form>
        </div>
    </div>
@endsection
