<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Installer')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { min-height: 100vh; background: radial-gradient(circle at top right, #dff6ee, #f7fafc 45%, #e9eef5); font-family: Vazirmatn, sans-serif; }
        .install-shell { max-width: 980px; margin: 2rem auto; }
        .install-card { border: 0; border-radius: 16px; box-shadow: 0 24px 50px rgba(15, 23, 42, .12); overflow: hidden; }
        .install-header { background: linear-gradient(120deg, #0f766e, #0ea5a3); color: #fff; padding: 1.25rem 1.5rem; }
        .step-badge { background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3); padding: .25rem .65rem; border-radius: 999px; font-size: .8rem; }
        .help-box { border: 1px dashed #8bc7b8; background: #f0fbf8; border-radius: 10px; padding: .9rem; }
        .install-footer { font-size: .9rem; color: #6b7280; }
        .wizard-steps { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem; }
        .wizard-chip {
            border: 1px solid #d1d5db;
            border-radius: 999px;
            padding: .35rem .7rem;
            font-size: .85rem;
            color: #4b5563;
            background: #fff;
            text-decoration: none;
        }
        .wizard-chip.active {
            border-color: #0f766e;
            background: #0f766e;
            color: #fff;
        }
        .progress.slim { height: .5rem; border-radius: 999px; }
        .resume-box { border: 1px solid #bae6fd; background: #f0f9ff; border-radius: 12px; padding: .75rem .9rem; }
    </style>
</head>
<body>
@php
    $wizardSteps = [
        ['slug' => 'welcome', 'label' => 'شروع'],
        ['slug' => 'requirements', 'label' => 'پیش نیازها'],
        ['slug' => 'database', 'label' => 'دیتابیس'],
        ['slug' => 'store-settings', 'label' => 'تنظیمات فروشگاه'],
        ['slug' => 'summary', 'label' => 'نصب نهایی'],
    ];
    $currentStep = trim((string) View::yieldContent('step_slug', 'welcome'));
    $activeIndex = collect($wizardSteps)->search(fn ($step) => $step['slug'] === $currentStep);
    $activeIndex = is_int($activeIndex) ? $activeIndex : 0;
    $progress = intval((($activeIndex + 1) / count($wizardSteps)) * 100);
@endphp
<div class="container install-shell">
    <div class="card install-card">
        <div class="install-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="fw-semibold">Nilak Installer</div>
                <div class="small opacity-75">نصب مرحله ای فروشگاه روی هاست cPanel + MySQL</div>
            </div>
            <div class="step-badge">@yield('step', 'Setup')</div>
        </div>
        <div class="card-body p-4 p-md-5">
            <div class="wizard-steps">
                @foreach($wizardSteps as $step)
                    <a href="{{ route('install.' . $step['slug']) }}"
                       class="wizard-chip {{ $currentStep === $step['slug'] ? 'active' : '' }}">
                        {{ $step['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="progress slim mb-3" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
            </div>

            @if($currentStep !== 'welcome')
                <div class="resume-box d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="small text-muted">اگر ارتباط قطع شد، نصب از آخرین مرحله ثبت شده ادامه پیدا می کند.</div>
                    <a href="{{ route('install.resume') }}" class="btn btn-sm btn-outline-primary">Resume</a>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    <div class="text-center mt-3 install-footer">اگر سیستم قبلا نصب شده باشد، این مسیر به صورت خودکار محدود می شود.</div>
</div>
</body>
</html>
