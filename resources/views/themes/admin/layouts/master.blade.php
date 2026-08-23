{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/admin/layouts/master.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل مدیریت نیلک')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: Vazirmatn, sans-serif; background-color: #f8f9fa; }
        header { background: #343a40; color: #fff; padding: 10px 20px; }
        .admin-brand { color: #fff; font-weight: 700; text-decoration: none; }
        .admin-tools { display: flex; align-items: center; gap: .75rem; }
        aside { background: #f1f1f1; min-height: calc(100vh - 65px); padding: 15px; }
        main { padding: 20px; }
        a { text-decoration: none; }
        .sidebar-link { display: block; padding: 8px 0; color: #333; }
        .sidebar-link:hover { color: #007bff; }
        [dir="rtl"] .sidebar-link { text-align: right; }
        .product-form .card-body { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .product-form .card-body > .mb-3 { margin-bottom: 0 !important; }
        .product-form .card-body > .mb-3:has(textarea) { grid-column: 1 / -1; }
        .product-form .card-body > .form-check { align-self: end; margin-bottom: .5rem; }
        .product-form .card-body > .row { grid-column: 1 / -1; }
        @media (max-width: 768px) {
            .product-form .card-body { grid-template-columns: 1fr; }
            .product-form .card-body > .mb-3:has(textarea), .product-form .card-body > .row { grid-column: auto; }
        }
    </style>
</head>
<body>

<header>
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">پنل مدیریت نیلک</a>
        <div class="admin-tools">
            <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm">فروشگاه</a>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">خروج</button>
            </form>
        </div>
    </div>
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
                    <li><a href="{{ route('admin.attributes.index') }}" class="sidebar-link">ویژگی‌های محصولات</a></li>
                    <li><a href="{{ route('admin.categories.index') }}" class="sidebar-link">مدیریت دسته‌بندی‌ها</a></li>
                    <li><a href="{{ route('admin.orders.index') }}" class="sidebar-link">مدیریت سفارش‌ها</a></li>
                    <li><a href="{{ route('admin.payments.index') }}" class="sidebar-link">مدیریت پرداخت‌ها</a></li>
                    <li><a href="{{ route('admin.payment-methods.index') }}" class="sidebar-link">روش‌های پرداخت</a></li>
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
    <small>© {{ app(\App\Support\DateFormatter::class)->format(now()) }} پنل مدیریت نیلک</small>
</footer>

</body>
</html>
