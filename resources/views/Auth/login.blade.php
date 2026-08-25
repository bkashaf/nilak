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
                            <label class="form-label" for="email">ایمیل</label>
                            <input id="email" type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" required autofocus>
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
@endsection
