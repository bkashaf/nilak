{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/default/layouts/shop.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('messages.brand'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="/themes/default/css/menu.css?v={{ filemtime(public_path('themes/default/css/menu.css')) }}">

    <style>
    body { background-color: #f8f9fa; font-family: Vazirmatn, sans-serif; text-align: start; margin:0; }
    .site-header { margin-bottom: 20px; }
    footer { margin-top: 40px; padding: 20px 0; background: #f4f4f4; border-top: 1px solid #ddd; }

    .site-header .navbar-nav { align-items: center; }
    .site-header .top-nav { position: relative; }

    /* در ستون (منوی موبایل) align-items روی محور inline اثر می‌گذارد و خودش با تغییر زبان/جهت هماهنگ می‌شود */
    .site-header .mobile-menu-panel .navbar-nav { align-items: stretch; }

    .site-tools { display: flex; align-items: center; gap: .35rem; flex-wrap: nowrap; position: relative; }
    .site-tools .nav-link { white-space: nowrap; }

    .cart-badge { min-width: 1.25rem; }
    .site-date { color: #6c757d; font-size: .875rem; white-space: nowrap; }

    .mobile-hamburger {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid #cfd8dc;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .mobile-hamburger svg { width: 16px; height: 16px; stroke: #111827; fill: none; stroke-width: 2; }

    .category-child { list-style: none; padding: 0; margin: 0; }

    @media (max-width: 767.98px) {
        body { padding-bottom: 64px; }
        .site-desktop-footer { display: none; }
        .desktop-nav { display: none !important; }
        .site-tools .nav-link { font-size: .82rem; padding-inline: .3rem; }
        .site-brand { font-size: 1.02rem; max-width: 44vw; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    }
    @media (min-width: 768px) { .mobile-bottom-nav { display: none !important; } }

    [dir="rtl"] .site-tools { margin-right: auto; }
    [dir="ltr"] .site-tools { margin-left: auto; }
    </style>
</head>
<body>

@php
    $menuService = app(\App\Support\MenuService::class);
    $fullMenu = $menuService->fullMenu();
@endphp

<header class="site-header bg-white shadow-sm">
    <div class="container">

        {{-- نوار بالایی --}}
        <nav class="navbar navbar-light py-2 top-nav d-flex align-items-center">

            {{-- دکمه موبایل --}}
            <button id="mobileMenuToggle" class="mobile-hamburger d-md-none me-2" aria-controls="mobileMenuPanel" aria-expanded="false" aria-label="باز کردن منو">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>

            {{-- برند --}}
            <a class="navbar-brand fw-bold me-0 site-brand" href="{{ route('home') }}">
                {{ __('messages.brand') }}
            </a>

            {{-- ابزارهای سایت --}}
            <ul class="navbar-nav site-tools flex-row flex-wrap">
                {{-- تاریخ --}}
                <li class="nav-item site-date d-none d-lg-flex align-items-center">
                    {{ app(\App\Support\DateFormatter::class)->format(now()) }}
                </li>

                {{-- سبد خرید --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cart.index') }}">
                        🛒 <span class="badge rounded-pill text-bg-primary cart-badge">
                            {{ app(\App\Domain\Cart\CartService::class)->items()->sum('quantity') }}
                        </span>
                    </a>
                </li>

                {{-- زبان --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('messages.language') }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'fa') }}">فارسی</a></li>
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">English</a></li>
                    </ul>
                </li>

                {{-- پروفایل --}}
                @include('themes.default.partials.profile-menu')

            </ul>

        </nav>

        {{-- منوی موبایل (پنل شناور) --}}
        <div id="mobileMenuPanel" class="mobile-menu-panel d-md-none" aria-hidden="true" role="menu">
            <ul class="navbar-nav flex-column">
                @foreach($fullMenu as $item)
                    @if($item['type'] === 'category')
                        <li class="nav-item">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <a class="nav-link category-root" href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                                @if(!empty($item['children']))
                                    <button type="button" class="category-toggle btn btn-sm" aria-expanded="false" aria-controls="cat-{{ $item['id'] ?? \Illuminate\Support\Str::slug($item['label']) }}">
                                        ▾
                                    </button>
                                @endif
                            </div>

                            @if(!empty($item['children']))
                                <ul id="cat-{{ $item['id'] ?? \Illuminate\Support\Str::slug($item['label']) }}" class="category-child">
                                    @foreach($item['children'] as $child)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('shop.index', ['category' => $child->slug]) }}">
                                                {{ $child->localized_name }}
                                            </a>

                                            @if(!empty($child->children))
                                                <ul class="category-child">
                                                    @foreach($child->children as $sub)
                                                        <li class="nav-item">
                                                            <a class="nav-link" href="{{ route('shop.index', ['category' => $sub->slug]) }}">
                                                                {{ $sub->localized_name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        {{-- منوی دسکتاپ --}}
        <nav class="navbar navbar-light border-top py-1 mt-3">
            <div class="desktop-nav w-100 d-none d-md-block">
                <ul class="navbar-nav primary-nav flex-row flex-wrap justify-content-start gap-1 w-100">
                    @foreach($fullMenu as $item)
                        @if($item['type'] === 'category')
                            <li class="nav-item dropdown category-menu">
                                <a class="nav-link dropdown-toggle" href="{{ $item['url'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $item['label'] }}
                                </a>
                                <div class="dropdown-menu">
                                    <div class="row g-3">
                                        @foreach($item['children'] as $child)
                                            <div class="col category-column">
                                                <a class="category-heading" href="{{ route('shop.index', ['category' => $child->slug]) }}">
                                                    {{ $child->localized_name }}
                                                </a>
                                                @foreach($child->children as $sub)
                                                    <a class="category-child" href="{{ route('shop.index', ['category' => $sub->slug]) }}">
                                                        {{ $sub->localized_name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </nav>

    </div>
</header>

<div class="container">
    @auth
        @if(!auth()->user()->isProfileComplete() && !request()->routeIs('account.profile.edit'))
            <div class="alert alert-warning mt-3 mb-3">
                برای ادامه خرید، تکمیل پروفایل الزامی است.
                <a href="{{ route('account.profile.edit') }}" class="alert-link">تکمیل پروفایل</a>
            </div>
        @endif
    @endauth

    @yield('content')
</div>

<footer class="site-desktop-footer">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="site-footer-title">{{ __('messages.brand') }}</div>
                <p class="text-muted mb-2">فروشگاه پوشاک و کفش نیلک با تمرکز بر خرید ساده، پشتیبانی سریع و ارسال مطمئن.</p>
                <div class="small text-muted">© {{ date('Y') }} {{ __('messages.footer') }}</div>
            </div>
            <div class="col-md-4">
                <div class="site-footer-title">لینک‌های مفید</div>
                <a class="site-footer-link" href="{{ route('home') }}">{{ __('messages.home') }}</a><br>
                <a class="site-footer-link" href="{{ route('shop.index') }}">{{ __('messages.shop') }}</a><br>
                <a class="site-footer-link" href="{{ route('order.tracking') }}">{{ __('messages.track_order') }}</a><br>
            </div>
            <div class="col-md-4">
                <div class="site-footer-title">تماس با ما</div>
                <div class="text-muted mb-1">آدرس: تهران، خیابان نمونه، پلاک ۱۰</div>
                <div class="text-muted mb-1">تلفن: ۰۲۱-۱۲۳۴۵۶۷۸</div>
                <div class="text-muted mb-3">ایمیل: support@nilak.local</div>
            </div>
        </div>
    </div>
</footer>

<nav class="mobile-bottom-nav d-flex justify-content-around py-2">
    <a class="d-flex flex-column align-items-center" href="{{ route('home') }}"><svg viewBox="0 0 24 24" width="20" height="20"><path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/></svg><span>خانه</span></a>
    <a class="d-flex flex-column align-items-center" href="{{ route('shop.index') }}"><svg viewBox="0 0 24 24" width="20" height="20"><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/><path d="M3 4h2l2.4 10.2a1 1 0 0 0 1 .8h9.7a1 1 0 0 0 1-.8L21 7H7"/></svg><span>فروشگاه</span></a>
    <a class="d-flex flex-column align-items-center" href="{{ route('cart.index') }}"><svg viewBox="0 0 24 24" width="20" height="20"><path d="M6 6h15l-1.5 9h-11z"/><path d="M6 6 5 3H2"/></svg><span>سبد</span></a>
    <a class="d-flex flex-column align-items-center" href="{{ route('order.tracking') }}"><svg viewBox="0 0 24 24" width="20" height="20"><path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h5"/></svg><span>پیگیری</span></a>
</nav>


</body>
</html>
