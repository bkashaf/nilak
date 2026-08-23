@extends('themes.admin.layouts.master')

@section('title', 'ایجاد کاربر')

@section('content')
    <h1>ایجاد کاربر جدید</h1>

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

    <form action="{{ route('admin.users.store') }}" method="POST" style="max-width:600px;">
        @csrf

        <div style="margin-bottom:8px;">
            <label>نام</label><br>
            <input type="text" name="name" value="{{ old('name') }}" style="width:100%; padding:6px;">
        </div>

        <div style="margin-bottom:8px;">
            <label>ایمیل</label><br>
            <input type="email" name="email" value="{{ old('email') }}" style="width:100%; padding:6px;">
        </div>

        <div style="margin-bottom:8px;">
            <label>رمز عبور</label><br>
            <input type="password" name="password" style="width:100%; padding:6px;">
        </div>

        <div style="margin-bottom:12px;">
            <label>تأیید رمز عبور</label><br>
            <input type="password" name="password_confirmation" style="width:100%; padding:6px;">
        </div>

        <button type="submit" style="padding:8px 12px;">ایجاد کاربر</button>
        <a href="{{ route('admin.users.index') }}" style="margin-left:10px;">انصراف</a>
    </form>
@endsection
