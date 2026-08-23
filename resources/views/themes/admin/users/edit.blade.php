@extends('themes.admin.layouts.master')

@section('title', 'ویرایش کاربر')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">ویرایش کاربر</h1>
            <p class="text-muted mb-0">اطلاعات حساب کاربری را به‌روزرسانی کنید.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">بازگشت</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="card shadow-sm" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="card-body p-4">
            <div class="mb-3">
                <label for="name" class="form-label">نام و نام خانوادگی</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" autocomplete="name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">ایمیل</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" autocomplete="email" required>
            </div>

        <!-- طعمه برای کاهش احتمال autofill -->
        <input type="text" name="fake_username" id="fake_username" style="display:none" autocomplete="username">

            <hr class="my-4">
            <h2 class="h5">تغییر رمز عبور</h2>
            <p class="text-muted small">برای حفظ رمز فعلی، این دو فیلد را خالی بگذارید.</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">رمز عبور جدید</label>
                    <input id="password" type="password" name="password" autocomplete="new-password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">تأیید رمز عبور</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="form-control">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary ms-2">انصراف</a>
            </div>
        </div>
    </form>
@endsection
