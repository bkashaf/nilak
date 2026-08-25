{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/default/layouts/shop.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('messages.brand'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background-color: #f8f9fa; font-family: Vazirmatn, sans-serif; text-align: start; }
        .site-header { margin-bottom: 20px; }
        footer { margin-top: 40px; padding: 20px 0; background: #f4f4f4; border-top: 1px solid #ddd; }
        .site-header .navbar-nav { align-items: center; }
        .site-header .top-nav { position: relative; }
        .site-tools { display: flex; align-items: center; gap: .35rem; flex-wrap: nowrap; }
        .site-tools .nav-link { white-space: nowrap; }
        .cart-badge { min-width: 1.25rem; }
        .site-date { color: #6c757d; font-size: .875rem; white-space: nowrap; }
        .site-tools .dropdown { position: relative; }
        .site-tools .dropdown-menu { position: absolute; z-index: 1030; inset-inline-end: 0; }
        .primary-nav .nav-link { color: #374151; font-weight: 600; border-radius: 999px; padding: .45rem .9rem; transition: all .18s ease; }
        .primary-nav .nav-link:hover { background: #e5f6f3; color: #0f766e; }
        .primary-nav .nav-link.active { background: #0f766e; color: #fff; }
        .category-menu { position: relative; }
        .category-menu > .dropdown-menu { position: absolute; z-index: 1030; min-width: 18rem; padding: 1rem; inset-inline-start: 0; }
        .category-menu .category-column { min-width: 9rem; }
        .category-menu .category-heading { font-weight: 700; color: #212529; }
        .category-menu .category-child { display: block; padding: .25rem 0; color: #6c757d; }
        .mobile-bottom-nav { position: fixed; bottom: 0; inset-inline: 0; z-index: 1040; background: #fff; border-top: 1px solid #ddd; }
        .mobile-bottom-nav a { color: #1f2937; text-decoration: none; font-size: .76rem; }
        .mobile-bottom-nav svg { width: 20px; height: 20px; stroke: #111827; fill: none; stroke-width: 1.8; }
        .site-footer-title { font-weight: 700; margin-bottom: .5rem; }
        .site-footer-link { color: #4b5563; text-decoration: none; display: inline-block; margin-bottom: .25rem; }
        .site-footer-link:hover { color: #111827; }
        [dir="rtl"] .site-desktop-footer { text-align: right; }
        [dir="ltr"] .site-desktop-footer { text-align: left; }
        .mobile-hamburger { width: 34px; height: 34px; border-radius: 50%; border: 1px solid #cfd8dc; background: #fff; display: inline-flex; align-items: center; justify-content: center; }
        .mobile-hamburger svg { width: 16px; height: 16px; stroke: #111827; fill: none; stroke-width: 2; }
        .mobile-menu-panel {
            position: absolute;
            top: calc(100% + 10px);
            inset-inline-start: 0;
            inset-inline-end: 0;
            z-index: 1060;
            border: 1px solid rgba(255, 255, 255, .35);
            background: rgba(255, 255, 255, .78);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            padding: .5rem .75rem;
            box-shadow: 0 20px 45px rgba(15, 23, 42, .18);
            opacity: 0;
            transform: translateY(-6px);
            pointer-events: none;
            transition: opacity .2s ease, transform .2s ease;
        }
        .mobile-menu-panel.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
        .mobile-menu-panel .nav-link { padding: .5rem .25rem; font-weight: 600; }
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

<header class="site-header bg-white shadow-sm">
    <div class="container">
        <nav class="navbar navbar-light py-2 top-nav">
            <button id="mobileMenuToggle" class="mobile-hamburger d-md-none me-2" type="button" aria-controls="mobileMenuPanel" aria-expanded="false" aria-label="Toggle navigation">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>

            <a class="navbar-brand fw-bold me-0 site-brand" href="{{ route('home') }}">
                {{ __('messages.brand') }}
            </a>

            @php
                $cartItemCount = app(\App\Domain\Cart\CartService::class)->items()->sum('quantity');
                $menuService = app(\App\Support\MenuService::class);
                $productCategoryRoot = $menuService->productCategoryRoot();
            @endphp
            <ul class="navbar-nav site-tools flex-row flex-wrap">
                <li class="nav-item site-date d-none d-lg-flex align-items-center">
                    {{ app(\App\Support\DateFormatter::class)->format(now()) }}
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cart.index') }}" aria-label="{{ __('messages.cart') }}">
                        <span aria-hidden="true">🛒</span>
                        <span class="visually-hidden">{{ __('messages.cart') }}</span>
                        <span class="badge rounded-pill text-bg-primary cart-badge">{{ $cartItemCount }}</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('messages.language') }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'fa') }}">{{ __('messages.persian') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">{{ __('messages.english') }}</a></li>
                    </ul>
                </li>
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            پروفایل کاربری
                        </a>
                        <ul class="dropdown-menu">
                            @if(auth()->user()->hasRole('admin'))
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">{{ __('messages.admin') }}</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('account.profile.edit') }}">پروفایل کاربری</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('messages.logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('messages.login') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">ثبت نام</a></li>
                @endauth
            </ul>

            <div id="mobileMenuPanel" class="mobile-menu-panel w-100 d-md-none">
                <ul class="navbar-nav flex-column">
                    @foreach($menuService->topLinks() as $link)
                        <li class="nav-item"><a class="nav-link {{ $link['active'] ? 'active' : '' }}" href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                    @endforeach
                    @if($productCategoryRoot)
                        <li class="nav-item"><a class="nav-link" href="{{ route('shop.index', ['category' => $productCategoryRoot->slug]) }}">{{ $productCategoryRoot->localized_name }}</a></li>
                    @endif
                </ul>
            </div>
        </nav>

        <nav class="navbar navbar-light border-top py-1" aria-label="{{ __('messages.main_navigation') }}">
            <div class="desktop-nav w-100 d-none d-md-block">
                <ul class="navbar-nav primary-nav flex-row flex-wrap justify-content-start gap-1 w-100">

                @foreach($menuService->topLinks() as $link)
                    <li class="nav-item"><a class="nav-link {{ $link['active'] ? 'active' : '' }}" href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                @endforeach

                {{-- دسته‌بندی محصولات و زیرشاخه‌های آن از دیتابیس --}}
                @if($productCategoryRoot)
                    <li class="nav-item dropdown category-menu">
                        <a class="nav-link dropdown-toggle" href="{{ route('shop.index', ['category' => $productCategoryRoot->slug]) }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $productCategoryRoot->localized_name }}
                        </a>
                        <div class="dropdown-menu">
                            <div class="row g-3">
                                @foreach($productCategoryRoot->children as $category)
                                    <div class="col category-column">
                                        <a class="category-heading" href="{{ route('shop.index', ['category' => $category->slug]) }}">{{ $category->localized_name }}</a>
                                        @foreach($category->children as $child)
                                            <a class="category-child" href="{{ route('shop.index', ['category' => $child->slug]) }}">{{ $child->localized_name }}</a>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </li>
                @endif

                </ul>
            </div>
        </nav>

    </div>
</header>


<div class="container">
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
                <a class="site-footer-link" href="{{ route('orders.track.form') }}">{{ __('messages.track_order') }}</a><br>
                <a class="site-footer-link" href="{{ route('checkout.index') }}">{{ __('messages.checkout') }}</a>
            </div>
            <div class="col-md-4">
                <div class="site-footer-title">تماس با ما</div>
                <div class="text-muted mb-1">آدرس: تهران، خیابان نمونه، پلاک ۱۰</div>
                <div class="text-muted mb-1">تلفن: ۰۲۱-۱۲۳۴۵۶۷۸</div>
                <div class="text-muted mb-3">ایمیل: support@nilak.local</div>
                <div class="d-flex gap-3 align-items-center">
                    <a href="#" class="site-footer-link" aria-label="ای‌نماد">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8M12 8v8"/></svg>
                    </a>
                    <a href="#" class="site-footer-link" aria-label="اتحادیه کسب و کار مجازی">
                        <svg viewBox="0 0 24 24"><path d="M4 6h16v12H4z"/><path d="M8 10h8M8 14h6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<nav class="mobile-bottom-nav d-flex justify-content-around py-2" aria-label="Mobile quick links">
    <a class="d-flex flex-column align-items-center" href="{{ route('home') }}"><svg viewBox="0 0 24 24"><path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/></svg><span>خانه</span></a>
    <a class="d-flex flex-column align-items-center" href="{{ route('shop.index') }}"><svg viewBox="0 0 24 24"><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/><path d="M3 4h2l2.4 10.2a1 1 0 0 0 1 .8h9.7a1 1 0 0 0 1-.8L21 7H7"/></svg><span>فروشگاه</span></a>
    <a class="d-flex flex-column align-items-center" href="{{ route('cart.index') }}"><svg viewBox="0 0 24 24"><path d="M6 6h15l-1.5 9h-11z"/><path d="M6 6 5 3H2"/></svg><span>سبد</span></a>
    <a class="d-flex flex-column align-items-center" href="{{ route('orders.track.form') }}"><svg viewBox="0 0 24 24"><path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h5"/></svg><span>پیگیری</span></a>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('mobileMenuToggle');
    var panel = document.getElementById('mobileMenuPanel');
    if (!toggle || !panel) return;
    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        panel.classList.toggle('show');
        toggle.setAttribute('aria-expanded', panel.classList.contains('show') ? 'true' : 'false');
    });
    document.addEventListener('click', function (event) {
        if (!panel.classList.contains('show')) return;
        if (panel.contains(event.target) || toggle.contains(event.target)) return;
        panel.classList.remove('show');
        toggle.setAttribute('aria-expanded', 'false');
    });
    panel.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            panel.classList.remove('show');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
});
</script>

</body>
</html>
