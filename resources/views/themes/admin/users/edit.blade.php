@extends('themes.admin.layouts.master')

@section('title', 'ویرایش کاربر')

@section('content')
    <h1>ویرایش کاربر</h1>

    @if (session('success'))
        <div style="color:green; margin-bottom:10px;">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div style="color:#b00020; margin-bottom:10px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST" style="max-width:600px;" autocomplete="off">
        @csrf
        @method('PUT')

        <div style="margin-bottom:8px;">
            <label>نام</label><br>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" style="width:100%; padding:6px;" autocomplete="name">
        </div>

        <div style="margin-bottom:8px;">
            <label>ایمیل</label><br>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" style="width:100%; padding:6px;" autocomplete="email">
        </div>

        <!-- طعمه برای کاهش احتمال autofill -->
        <input type="text" name="fake_username" id="fake_username" style="display:none" autocomplete="username">

        <div style="margin-bottom:8px;">
            <label>رمز عبور (در صورت تغییر)</label><br>
            <input type="password" name="password" autocomplete="new-password" style="width:100%; padding:6px;" value="">
        </div>

        <div style="margin-bottom:12px;">
            <label>تأیید رمز عبور</label><br>
            <input type="password" name="password_confirmation" autocomplete="new-password" style="width:100%; padding:6px;" value="">
        </div>

        <button type="submit" style="padding:8px 12px;">ذخیره تغییرات</button>
        <a href="{{ route('admin.users.index') }}" style="margin-left:10px;">انصراف</a>
    </form>
@endsection
