@extends('themes.default.layouts.shop')

@section('title', 'ورود به حساب')

@section('content')
    <div class="row justify-content-center py-4">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 mb-2">ورود به حساب کاربری</h1>
                    <p class="text-muted mb-4">برای ادامه خرید وارد حساب خود شوید.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}" class="row g-3">
                        @csrf

                        <div class="col-12">
                            <label class="form-label" for="mobile">شماره موبایل یا ایمیل ادمین</label>
                            <div class="input-group" dir="ltr">
                                <select name="country_code" id="country_code" class="form-select" style="max-width: 160px; text-align: left;" required>
                                    <option value="+98" @selected(old('country_code', $defaultCountryCode) === '+98')>🇮🇷 +98</option>
                                    <option value="+1" @selected(old('country_code', $defaultCountryCode) === '+1')>🇺🇸 +1</option>
                                </select>
                                <input id="mobile" type="text" name="mobile" class="form-control form-control-lg js-mobile-en" value="{{ old('mobile') }}" dir="ltr" style="text-align: left;" placeholder="0912... یا admin@example.com" required autofocus>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="password">کلمه عبور</label>
                            <input id="password" type="password" name="password" class="form-control form-control-lg" required>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input id="remember" type="checkbox" name="remember" class="form-check-input">
                                <label for="remember" class="form-check-label">مرا به خاطر بسپار</label>
                            </div>
                        </div>

                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">ورود</button>
                        </div>
                    </form>

                    <hr class="my-4">
                    <p class="mb-0 text-muted">حساب ندارید؟ <a href="{{ route('register') }}" class="fw-semibold">ثبت نام</a></p>
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

        const mobile = document.querySelector('.js-mobile-en');
        if (!mobile) return;

        mobile.addEventListener('input', function () {
            if (this.value.indexOf('@') !== -1) {
                return;
            }

            this.value = normalizeDigits(this.value);
        });

        mobile.addEventListener('blur', function () {
            if (this.value.indexOf('@') !== -1) {
                return;
            }

            this.value = normalizeDigits(this.value).replace(/[^0-9+]/g, '');
        });
    });
    </script>
@endsection
