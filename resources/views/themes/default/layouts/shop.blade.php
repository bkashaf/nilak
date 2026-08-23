{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/default/layouts/shop.blade.php --}}
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'فروشگاه نیلک')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background-color: #f8f9fa; font-family: Vazirmatn, sans-serif; }
        .navbar { margin-bottom: 20px; }
        footer { margin-top: 40px; padding: 20px 0; background: #eee; text-align: center; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">

        {{-- لوگو --}}
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            فروشگاه نیلک
        </a>

        {{-- دکمه موبایل --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- منو --}}
        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav ms-auto">

                {{-- خانه --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        خانه
                    </a>
                </li>

                {{-- فروشگاه --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop.index') ? 'active' : '' }}" href="{{ route('shop.index') }}">
                        فروشگاه
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
                        سبد خرید
                    </a>
                </li>

                {{-- تسویه حساب --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('checkout.index') ? 'active' : '' }}" href="{{ route('checkout.index') }}">
                        تسویه حساب
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>


<div class="container">
    @yield('content')
</div>

<footer>
    <p>© {{ date('Y') }} فروشگاه نیلک</p>
</footer>

</body>
</html>
