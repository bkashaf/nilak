{{-- View: C:/xampp/htdocs/nilak/resources/views/themes/default/partials/profile-menu.blade.php --}}
@auth
    <li class="nav-item dropdown site-profile-dropdown">
        <a id="profileToggle"
           class="nav-link dropdown-toggle"
           href="#"
           role="button"
           data-bs-toggle="dropdown"
           data-bs-display="static"
           aria-expanded="false"
           aria-haspopup="true">
            پروفایل کاربری
        </a>

        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileToggle">
            @if(auth()->user()->hasRole('admin'))
                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">پنل مدیریت</a></li>
            @endif
            <li><a class="dropdown-item" href="{{ route('account.profile.edit') }}">پروفایل کاربری</a></li>
            <li><a class="dropdown-item" href="{{ route('account.orders') }}">سفارش‌های من</a></li>
            <li>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item">خروج</button>
                </form>
            </li>
        </ul>
    </li>
@else
    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">ورود</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">ثبت نام</a></li>
@endauth
