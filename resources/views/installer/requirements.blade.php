@extends('installer.layout')

@section('title', 'Installer | Requirements')
@section('step', 'Step 2/5')
@section('step_slug', 'requirements')

@section('content')
    <h1 class="h3 mb-3">بررسی پیش نیازها</h1>
    <p class="text-muted mb-4">در این مرحله سازگاری سرور برای نصب بررسی می شود.</p>

    <div class="table-responsive mb-4">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>آیتم</th>
                    <th>وضعیت</th>
                    <th>جزئیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checks as $check)
                    <tr>
                        <td>{{ $check['label'] }}</td>
                        <td>
                            @php
                                $isWarning = ($check['level'] ?? 'required') === 'warning';
                                $badgeClass = $check['status']
                                    ? 'text-bg-success'
                                    : ($isWarning ? 'text-bg-warning' : 'text-bg-danger');
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ $check['status'] ? 'OK' : ($isWarning ? 'WARN' : 'FAIL') }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $check['detail'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(!$allOk)
        <div class="alert alert-danger mb-4">برخی پیش نیازها برقرار نیست. ابتدا موارد Fail را در هاست اصلاح کنید و صفحه را دوباره بارگذاری کنید.</div>
    @elseif(($warningCount ?? 0) > 0)
        <div class="alert alert-warning mb-4">پیش نیازهای اجباری کامل هستند؛ اما {{ $warningCount }} هشدار برای SSL/DocumentRoot وجود دارد. نصب ادامه می یابد ولی پیشنهاد می شود قبل از نهایی سازی آن ها را اصلاح کنید.</div>
    @endif

    <div class="help-box mb-4">
        <div class="fw-semibold mb-2">راهنمای عملی برای cPanel</div>
        <ul class="mb-0">
            <li>Document Root دامنه را به پوشه public پروژه تنظیم کنید.</li>
            <li>مطمئن شوید storage و bootstrap/cache قابلیت نوشتن دارند.</li>
            <li>اگر SSL فعال است، APP_URL را با https وارد کنید.</li>
            <li>برای زمان‌بندی وظایف، Cron cPanel را برای دستور <span dir="ltr">php artisan schedule:run</span> تنظیم کنید.</li>
        </ul>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('install.welcome') }}" class="btn btn-outline-secondary">مرحله قبل</a>
        <a href="{{ route('install.database') }}" class="btn btn-primary {{ $allOk ? '' : 'disabled' }}">مرحله بعد: تنظیم دیتابیس</a>
    </div>
@endsection
