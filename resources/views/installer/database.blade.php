@extends('installer.layout')

@section('title', 'Installer | Database')
@section('step', 'Step 3/5')
@section('step_slug', 'database')

@section('content')
    <h1 class="h3 mb-3">تنظیم پایگاه داده و حساب مدیر</h1>
    <p class="text-muted mb-4">اطلاعات MySQL و مدیر اولیه را وارد کنید؛ پس از تست موفق، به مرحله تنظیمات فروشگاه هدایت می شوید.</p>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('install.database.test') }}" class="row g-3">
        @csrf

        <div class="col-md-4">
            <label class="form-label">DB Host</label>
            <input name="db_host" class="form-control" value="{{ old('db_host', $defaults['db_host']) }}" required>
        </div>

        <div class="col-md-2">
            <label class="form-label">DB Port</label>
            <input name="db_port" class="form-control" value="{{ old('db_port', $defaults['db_port']) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">DB Name</label>
            <input name="db_database" class="form-control" value="{{ old('db_database', $defaults['db_database']) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">DB Username</label>
            <input name="db_username" class="form-control" value="{{ old('db_username', $defaults['db_username']) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">DB Password</label>
            <input type="password" name="db_password" class="form-control" value="{{ old('db_password', $defaults['db_password']) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">APP URL</label>
            <input name="app_url" class="form-control" value="{{ old('app_url', $defaults['app_url']) }}" required>
        </div>

        <div class="col-12"><hr></div>

        <div class="col-md-4">
            <label class="form-label">Admin Name</label>
            <input name="admin_name" class="form-control" value="{{ old('admin_name', $defaults['admin_name']) }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Admin Email</label>
            <input name="admin_email" type="email" class="form-control" value="{{ old('admin_email', $defaults['admin_email']) }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Admin Mobile</label>
            <input name="admin_mobile" class="form-control" value="{{ old('admin_mobile') }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Admin Password</label>
            <input name="admin_password" type="password" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Confirm Admin Password</label>
            <input name="admin_password_confirmation" type="password" class="form-control" required>
        </div>

        <div class="col-12 help-box">
            راهنمای cPanel: Database Wizard را باز کنید، دیتابیس بسازید، کاربر بسازید، کاربر را به دیتابیس Add کنید و Full Privileges بدهید. سپس مقادیر بالا را عینا وارد کنید.
        </div>

        <div class="col-12 d-flex gap-2 flex-wrap">
            <a href="{{ route('install.requirements') }}" class="btn btn-outline-secondary">مرحله قبل</a>
            <button class="btn btn-primary">تست اتصال و ادامه به تنظیمات فروشگاه</button>
        </div>
    </form>
@endsection
