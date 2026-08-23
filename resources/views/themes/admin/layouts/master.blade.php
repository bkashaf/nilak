{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/admin/layouts/master.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل مدیریت نیلاک')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: Vazirmatn, sans-serif; background-color: #f8f9fa; }
        header { background: #343a40; color: #fff; padding: 10px 20px; }
        aside { background: #f1f1f1; min-height: calc(100vh - 65px); padding: 15px; }
        main { padding: 20px; }
        a { text-decoration: none; }
        .sidebar-link { display: block; padding: 8px 0; color: #333; }
        .sidebar-link:hover { color: #007bff; }
        [dir="rtl"] .sidebar-link { text-align: right; }
    </style>
</head>
<body>

<header class="d-flex justify-content-between align-items-center">
    <h2 class="m-0">پنل مدیریت نیلاک</h2>
    <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm">فروشگاه</a>
    <form action="{{ route('logout') }}" method="POST" class="m-0">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm">خروج</button>
    </form>
</header>

<div class="container-fluid">
    <div class="row">

        {{-- سایدبار --}}
        <aside class="col-md-2 border-end">
            <nav>
                <ul class="list-unstyled">
                    <li><a href="{{ route('admin.dashboard') }}" class="sidebar-link">داشبورد</a></li>
                    <li><a href="{{ route('admin.users.index') }}" class="sidebar-link">مدیریت کاربران</a></li>
                    <li><a href="{{ route('admin.products.index') }}" class="sidebar-link">مدیریت محصولات</a></li>
                    <li><a href="{{ route('admin.categories.index') }}" class="sidebar-link">مدیریت دسته‌بندی‌ها</a></li>
                    <li><a href="{{ route('admin.orders.index') }}" class="sidebar-link">مدیریت سفارش‌ها</a></li>
                    <li><a href="{{ route('admin.payments.index') }}" class="sidebar-link">مدیریت پرداخت‌ها</a></li>
                    <li><a href="{{ route('admin.reports.index') }}" class="sidebar-link">گزارش‌ها</a></li>
                    <li><a href="{{ route('admin.settings.index') }}" class="sidebar-link">تنظیمات</a></li>
                </ul>
            </nav>
        </aside>

        {{-- محتوای اصلی --}}
        <main class="col-md-10">
            @yield('content')
        </main>

    </div>
</div>

<footer class="text-center mt-4 py-3 bg-light border-top">
    <small>© {{ app(\App\Support\DateFormatter::class)->format(now()) }} پنل مدیریت نیلاک</small>
</footer>

</body>
</html>
