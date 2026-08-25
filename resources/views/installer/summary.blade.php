@extends('installer.layout')

@section('title', 'Installer | Summary')
@section('step', 'Step 5/5')
@section('step_slug', 'summary')

@section('content')
    <h1 class="h3 mb-3">خلاصه نصب و اجرای نهایی</h1>

    @if(($result['ok'] ?? false))
        <div class="alert alert-success">{{ $result['message'] ?? 'اتصال موفق بود.' }}</div>
    @else
        <div class="alert alert-danger">{{ $result['message'] ?? 'اتصال ناموفق بود.' }}</div>
    @endif

    <div class="help-box mb-4">
        <div class="fw-semibold mb-2">پیشنمایش .env</div>
        <pre class="mb-0 small" style="white-space: pre-wrap;">{{ implode("\n", $envPreview) }}</pre>
    </div>

    <div class="card border-0 bg-light mb-4">
        <div class="card-body">
            <h2 class="h6 mb-3">خلاصه تنظیمات فروشگاه</h2>
            <div class="row g-2 small">
                <div class="col-md-6"><strong>Store Name:</strong> {{ $store['store_name'] ?? '-' }}</div>
                <div class="col-md-3"><strong>Locale:</strong> {{ $store['default_locale'] ?? '-' }}</div>
                <div class="col-md-3"><strong>Timezone:</strong> {{ $store['timezone'] ?? '-' }}</div>
                <div class="col-md-6"><strong>Currency:</strong> {{ $store['currency_label'] ?? '-' }}</div>
                <div class="col-md-6"><strong>Logo:</strong> {{ !empty($store['store_logo_path']) ? $store['store_logo_path'] : 'Not set' }}</div>
            </div>
        </div>
    </div>

    <div class="alert alert-warning">
        با اجرای نصب نهایی، فایل .env نوشته می شود، کلید برنامه ساخته می شود، دیتابیس migrate/seed می شود، ادمین اولیه ساخته می شود و نصب قفل می شود.
    </div>

    <form method="POST" action="{{ route('install.run') }}" class="card border-0 bg-light mb-4">
        @csrf
        <div class="card-body">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="confirm_apply" name="confirm_apply" required>
                <label class="form-check-label" for="confirm_apply">
                    تایید می کنم اطلاعات بالا صحیح است و نصب واقعی انجام شود.
                </label>
            </div>

            <button class="btn btn-success">اجرای نصب نهایی</button>
        </div>
    </form>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('install.store-settings') }}" class="btn btn-outline-secondary">بازگشت به تنظیمات فروشگاه</a>
        <a href="{{ route('install.database') }}" class="btn btn-outline-secondary">بازگشت به تنظیم دیتابیس</a>
    </div>
@endsection
