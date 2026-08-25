@extends('installer.layout')

@section('title', 'Installer | Completed')
@section('step', 'Done')

@section('content')
    @if(!empty($hasError))
        <h1 class="h3 mb-3 text-danger">نصب با خطا متوقف شد</h1>
        <p class="text-muted mb-4">گزارش زیر را بررسی کنید، مشکل را رفع کنید و دوباره تلاش کنید.</p>
    @else
        <h1 class="h3 mb-3 text-success">نصب با موفقیت انجام شد</h1>
        <p class="text-muted mb-4">سیستم آماده استفاده است. فایل نصب قفل شده تا از نصب مجدد ناخواسته جلوگیری شود.</p>
    @endif

    <div class="card border-0 bg-light mb-4">
        <div class="card-body">
            <h2 class="h6 mb-3">گزارش اجرای نصب</h2>
            <ul class="list-group list-group-flush">
                @foreach($report as $item)
                    <li class="list-group-item px-0 bg-transparent">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>{{ $item['step'] }}</strong>
                            <span class="badge {{ !empty($item['ok']) ? 'text-bg-success' : 'text-bg-danger' }}">{{ !empty($item['ok']) ? 'OK' : 'FAIL' }}</span>
                        </div>
                        <div class="small text-muted">{{ $item['message'] ?? '' }}</div>
                        @if(!empty($item['output']))
                            <pre class="small mt-2 mb-0" style="white-space: pre-wrap;">{{ $item['output'] }}</pre>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('home') }}" class="btn btn-primary">ورود به سایت</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">ورود به پنل مدیریت</a>
    </div>
@endsection
