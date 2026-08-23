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
        footer { margin-top: 40px; padding: 20px 0; background: #eee; text-align: center; }
        .site-header .navbar-nav { align-items: center; }
        .site-tools { display: flex; align-items: center; gap: .5rem; }
        .site-tools .nav-link { white-space: nowrap; }
        .cart-badge { min-width: 1.25rem; }
        .site-date { color: #6c757d; font-size: .875rem; white-space: nowrap; }
        .site-tools .dropdown { position: relative; }
        .site-tools .dropdown-menu { position: absolute; z-index: 1030; inset-inline-end: 0; }
        [dir="rtl"] .site-tools { margin-right: auto; }
        [dir="ltr"] .site-tools { margin-left: auto; }
    </style>
</head>
<body>

<header class="site-header bg-white shadow-sm">
    <div class="container">
        <nav class="navbar navbar-light py-2">
            <a class="navbar-brand fw-bold me-0" href="{{ route('home') }}">
                {{ __('messages.brand') }}
            </a>

            @php
                $cartItemCount = app(\App\Domain\Cart\CartService::class)->items()->sum('quantity');
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
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            @if(auth()->user()->hasRole('admin'))
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">{{ __('messages.admin') }}</a></li>
                            @endif
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
                @endauth
            </ul>
        </nav>

        <nav class="navbar navbar-expand-md navbar-light border-top py-1" aria-label="{{ __('messages.main_navigation') }}">
            <div id="mainMenu" class="w-100">
                <ul class="navbar-nav flex-row flex-wrap justify-content-start gap-1">

                {{-- خانه --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        {{ __('messages.home') }}
                    </a>
                </li>

                {{-- فروشگاه --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop.index') ? 'active' : '' }}" href="{{ route('shop.index') }}">
                        {{ __('messages.shop') }}
                    </a>
                </li>

                {{-- دسته‌بندی‌ها (داینامیک) --}}
                @php
    $categories = \App\Models\Category::active()->orderBy('position')->get();
@endphp

@foreach($categories as $cat)
    <li class="nav-item">
        <a class="nav-link" href="{{ route('shop.index', ['category' => $cat->slug]) }}">
            {{ $cat->name }}
        </a>
    </li>
@endforeach


                {{-- سبد خرید --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                        {{ __('messages.cart') }}
                    </a>
                </li>

                {{-- تسویه حساب --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('checkout.index') ? 'active' : '' }}" href="{{ route('checkout.index') }}">
                        {{ __('messages.checkout') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('orders.track*') ? 'active' : '' }}" href="{{ route('orders.track.form') }}">
                        پیگیری سفارش
                    </a>
                </li>

                </ul>
            </div>
        </nav>

    </div>
</header>


<div class="container">
    @yield('content')
</div>

<footer>
    <p>© {{ date('Y') }} {{ __('messages.footer') }}</p>
</footer>

</body>
</html>
