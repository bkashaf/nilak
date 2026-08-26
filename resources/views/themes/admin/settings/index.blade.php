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

                <div class="col-12 mt-2">
                    <hr>
                    <h2 class="h5 mb-1">صفحه پیش فرض سایت</h2>
                    <p class="text-muted mb-0">مشخص کنید بازدیدکننده در آدرس اصلی سایت (/) کدام صفحه را ببیند.</p>
                </div>

                <div class="col-md-6">
                    <label for="default_landing_target" class="form-label">نمایش پیش فرض صفحه اصلی</label>
                    <select id="default_landing_target" name="default_landing_target" class="form-select" required>
                        <option value="home" @selected(old('default_landing_target', $settings['default_landing_target']) === 'home')>خانه فروشگاه (Home)</option>
                        <option value="shop" @selected(old('default_landing_target', $settings['default_landing_target']) === 'shop')>فروشگاه (Shop)</option>
                        <option value="page" @selected(old('default_landing_target', $settings['default_landing_target']) === 'page')>یک صفحه سفارشی</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="default_landing_page_id" class="form-label">Page ID (در حالت صفحه سفارشی)</label>
                    <input id="default_landing_page_id" name="default_landing_page_id" type="number" min="1" value="{{ old('default_landing_page_id', $settings['default_landing_page_id']) }}" class="form-control" placeholder="مثال: 12">
                    <div class="form-text">اگر حالت «یک صفحه سفارشی» را انتخاب کنید، این شناسه باید معتبر باشد.</div>
                </div>

                <div class="col-12">
                    <label for="landing_page_picker" class="form-label">انتخاب سریع از صفحات موجود</label>
                    <select id="landing_page_picker" class="form-select">
                        <option value="">-- انتخاب صفحه برای پرکردن خودکار Page ID --</option>
                        @foreach($pages as $page)
                            <option value="{{ $page->id }}">#{{ $page->id }} - {{ $page->title }} ({{ $page->slug }}) {{ $page->is_published ? '' : '[Draft]' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 mt-2">
                    <hr>
                    <h2 class="h5 mb-1">تنظیمات پنل پیامک</h2>
                    <p class="text-muted mb-0">برای فاز OTP و اعلان های پیامکی آماده سازی شود.</p>
                </div>

                <div class="col-md-6">
                    <label for="sms_provider" class="form-label">ارائه دهنده SMS</label>
                    <select id="sms_provider" name="sms_provider" class="form-select" required>
                        <option value="none" @selected(old('sms_provider', $settings['sms_provider']) === 'none')>None (غیرفعال)</option>
                        <option value="kavenegar" @selected(old('sms_provider', $settings['sms_provider']) === 'kavenegar')>Kavenegar</option>
                        <option value="melipayamak" @selected(old('sms_provider', $settings['sms_provider']) === 'melipayamak')>MeliPayamak</option>
                        <option value="custom" @selected(old('sms_provider', $settings['sms_provider']) === 'custom')>Custom</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="sms_sender" class="form-label">شماره/لاین ارسال</label>
                    <input id="sms_sender" name="sms_sender" value="{{ old('sms_sender', $settings['sms_sender']) }}" class="form-control" placeholder="3000xxxx">
                </div>

                <div class="col-md-6">
                    <label for="sms_api_key" class="form-label">API Key / Token</label>
                    <input id="sms_api_key" name="sms_api_key" value="{{ old('sms_api_key', $settings['sms_api_key']) }}" class="form-control" dir="ltr">
                </div>

                <div class="col-md-6">
                    <label for="sms_endpoint" class="form-label">API Endpoint</label>
                    <input id="sms_endpoint" name="sms_endpoint" value="{{ old('sms_endpoint', $settings['sms_endpoint']) }}" class="form-control" dir="ltr" placeholder="https://api.example.com/send">
                </div>

                <div class="col-md-6">
                    <label for="sms_username" class="form-label">نام کاربری پنل</label>
                    <input id="sms_username" name="sms_username" value="{{ old('sms_username', $settings['sms_username']) }}" class="form-control" dir="ltr">
                </div>

                <div class="col-md-6">
                    <label for="sms_password" class="form-label">رمز پنل</label>
                    <input id="sms_password" name="sms_password" value="{{ old('sms_password', $settings['sms_password']) }}" class="form-control" dir="ltr">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">ذخیره تنظیمات</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const target = document.getElementById('default_landing_target');
        const pageIdInput = document.getElementById('default_landing_page_id');
        const picker = document.getElementById('landing_page_picker');

        function syncPageIdState() {
            const enabled = target && target.value === 'page';
            if (!pageIdInput) return;

            pageIdInput.disabled = !enabled;
            pageIdInput.classList.toggle('bg-light', !enabled);
        }

        if (target) {
            target.addEventListener('change', syncPageIdState);
            syncPageIdState();
        }

        if (picker && pageIdInput) {
            picker.addEventListener('change', function () {
                if (!this.value) return;

                pageIdInput.value = this.value;
                if (target) {
                    target.value = 'page';
                    syncPageIdState();
                }
            });
        }
    });
    </script>
@endsection
