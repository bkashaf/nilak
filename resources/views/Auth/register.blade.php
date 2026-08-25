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
                            <input id="mobile" type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="email">ایمیل (اختیاری)</label>
                            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="password">کلمه عبور <span class="text-danger">*</span></label>
                            <input id="password" type="password" name="password" class="form-control" required>
                            <div class="form-text">حداقل 8 کاراکتر و شامل حداقل یک حرف انگلیسی و یک عدد باشد.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">تکرار کلمه عبور <span class="text-danger">*</span></label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
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
@endsection
