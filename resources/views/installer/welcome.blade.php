@extends('installer.layout')

@section('title', 'Installer | Welcome')
@section('step', 'Step 1/5')
@section('step_slug', 'welcome')

@section('content')
    <h1 class="h3 mb-3">شروع نصب فروشگاه</h1>
    <p class="text-muted mb-4">این راهنما شما را برای نصب سیستم روی cPanel به شکل ساده و روان همراهی می کند.</p>

    <div class="help-box mb-4">
        <div class="fw-semibold mb-2">قبل از شروع:</div>
        <ul class="mb-0">
            <li>در cPanel یک دیتابیس MySQL و کاربر بسازید و Full Privileges بدهید.</li>
            <li>اطلاعات DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD را آماده کنید.</li>
            <li>دامنه یا ساب دامنه ای که پروژه روی آن اجرا می شود را مشخص کنید (APP_URL).</li>
        </ul>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('install.requirements') }}" class="btn btn-primary">بررسی پیش نیازها</a>
        @if(!empty($resumeUrl) && $resumeUrl !== route('install.requirements'))
            <a href="{{ route('install.resume') }}" class="btn btn-outline-primary">ادامه نصب قبلی</a>
        @endif
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">بازگشت به سایت</a>
    </div>
@endsection
