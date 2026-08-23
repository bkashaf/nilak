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
        .navbar { margin-bottom: 20px; }
        footer { margin-top: 40px; padding: 20px 0; background: #eee; text-align: center; }
        [dir="rtl"] .navbar-nav { margin-right: auto !important; margin-left: 0 !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-md navbar-light bg-light shadow-sm">
    <div class="container">

        {{-- لوگو --}}
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            {{ __('messages.brand') }}
        </a>

        {{-- منو --}}
        <div id="mainMenu" class="flex-grow-1">
            <ul class="navbar-nav ms-auto flex-row flex-wrap justify-content-end gap-1">

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

                <li class="nav-item dropdown ms-md-3">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('messages.language') }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'fa') }}">{{ __('messages.persian') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">{{ __('messages.english') }}</a></li>
                    </ul>
                </li>
            </ul>
        </div>

    </div>
</nav>


<div class="container">
    @yield('content')
</div>

<footer>
    <p>© {{ date('Y') }} {{ __('messages.footer') }}</p>
</footer>

</body>
</html>
