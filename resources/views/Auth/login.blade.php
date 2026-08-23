<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>ورود</title>
</head>
<body>
    <h1>ورود به سیستم</h1>

    @if ($errors->any())
        <div style="color:red;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div>
            <label>ایمیل:</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>

        <div>
            <label>رمز عبور:</label>
            <input type="password" name="password">
        </div>

        <div>
            <label>
                <input type="checkbox" name="remember"> مرا به خاطر بسپار
            </label>
        </div>

        <button type="submit">ورود</button>
    </form>
</body>
</html>
