{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/default/layouts/shop.blade.php --}}
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'فروشگاه نیلاک')</title>

    {{-- Bootstrap --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; font-family: Vazirmatn, sans-serif; }
        .navbar { margin-bottom: 20px; }
        footer { margin-top: 40px; padding: 20px 0; background: #eee; text-align: center; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('shop.index') }}">فروشگاه نیلاک</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#shopMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="shopMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('shop.index') }}">خانه</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">سبد خرید</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('checkout.index') }}">تسویه حساب</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>

<footer>
    <p>© {{ date('Y') }} فروشگاه نیلاک</p>
</footer>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
