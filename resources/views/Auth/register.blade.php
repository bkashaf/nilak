@extends('themes.default.layouts.shop')

@section('title', 'ثبت نام')

@section('content')
    <div class="row justify-content-center py-4">
        <div class="col-lg-6 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 mb-2">ایجاد حساب کاربری</h1>
                    <p class="text-muted mb-4">برای شروع فقط شماره موبایل و کلمه عبور لازم است. تکمیل پروفایل برای خرید الزامی خواهد بود.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('register.post') }}" class="row g-3">
                        @csrf

                        <div class="col-12">
                            <label class="form-label" for="mobile">شماره موبایل <span class="text-danger">*</span></label>
                            <div class="input-group" dir="ltr">
                                <select name="country_code" id="country_code" class="form-select" style="max-width: 94px; text-align: left;" required>
                                    <option value="+98" @selected(old('country_code', $defaultCountryCode) === '+98')>🇮🇷 +98</option>
                                    <option value="+1" @selected(old('country_code', $defaultCountryCode) === '+1')>🇺🇸 +1</option>
                                </select>
                                <input id="mobile" type="text" name="mobile" class="form-control js-mobile-en" value="{{ old('mobile') }}" inputmode="numeric" dir="ltr" style="text-align: left;" placeholder="09121234567" required>
                            </div>
                            <div class="form-text">شماره را می توانید به صورت کامل (مثل 0912...) یا بدون صفر ابتدایی وارد کنید. صفر ابتدایی بعد از کد کشور نادیده گرفته می شود.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="email">ایمیل (اختیاری)</label>
                            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="password">کلمه عبور <span class="text-danger">*</span></label>
                            <input id="password" type="password" name="password" class="form-control" required>
                            <div class="alert alert-info mt-2 mb-0 py-2 px-3 small" role="alert">
                                فرمت معتبر کلمه عبور:
                                حداقل 8 کاراکتر، حداقل یک حرف بزرگ انگلیسی (A-Z)، حداقل یک حرف کوچک انگلیسی (a-z)، و حداقل یک عدد (0-9).
                                مثال: Nilak1234
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">تکرار کلمه عبور <span class="text-danger">*</span></label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="captcha_answer">کپچای ساده <span class="text-danger">*</span></label>
                            <div class="input-group" dir="ltr">
                                <span class="input-group-text" dir="ltr">{{ $captchaA }} + {{ $captchaB }} = ?</span>
                                <input id="captcha_answer" type="text" name="captcha_answer" class="form-control js-number-en" value="{{ old('captcha_answer') }}" inputmode="numeric" dir="ltr" style="text-align: left;" required>
                            </div>
                            <div class="form-text">برای ادامه، پاسخ عبارت ریاضی را با اعداد انگلیسی وارد کنید.</div>
                        </div>

                        <div class="col-12 d-grid d-md-flex justify-content-md-end gap-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">بازگشت به ورود</a>
                            <button type="submit" class="btn btn-primary">ایجاد حساب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = {
            '۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
            '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'
        };

        function normalizeDigits(value) {
            return (value || '').replace(/[۰-۹٠-٩]/g, function (ch) { return map[ch] || ch; });
        }

        document.querySelectorAll('.js-mobile-en').forEach(function (el) {
            el.addEventListener('input', function () {
                this.value = normalizeDigits(this.value);
            });
            el.addEventListener('blur', function () {
                this.value = normalizeDigits(this.value).replace(/[^0-9+]/g, '');
            });
        });

        document.querySelectorAll('.js-number-en').forEach(function (el) {
            el.addEventListener('input', function () {
                this.value = normalizeDigits(this.value);
            });
            el.addEventListener('blur', function () {
                this.value = normalizeDigits(this.value).replace(/[^0-9]/g, '');
            });
        });
    });
    </script>
@endsection
