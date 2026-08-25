@extends('themes.default.layouts.shop')

@section('title', 'پروفایل کاربری')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">پروفایل کاربری</h1>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning">{{ session('warning') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    @if(!$user->isProfileComplete())
                        <div class="alert alert-info">
                            برای فعال شدن خرید، تکمیل موارد ستاره دار الزامی است.
                            @php($missing = $user->profileMissingFields())
                            @if(!empty($missing))
                                <div class="small mt-1 text-muted">موارد ناقص: {{ implode('، ', $missing) }}</div>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('account.profile.update') }}" class="row g-3">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label for="first_name" class="form-label">نام <span class="text-danger">*</span></label>
                            <input id="first_name" name="first_name" class="form-control" required value="{{ old('first_name', $user->first_name) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label">نام خانوادگی <span class="text-danger">*</span></label>
                            <input id="last_name" name="last_name" class="form-control" required value="{{ old('last_name', $user->last_name) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="username" class="form-label">نام کاربری (اختیاری)</label>
                            <input id="username" name="username" class="form-control" value="{{ old('username', $user->username) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="mobile" class="form-label">شماره موبایل <span class="text-danger">*</span></label>
                            <input id="mobile" name="mobile" class="form-control" required value="{{ old('mobile', $user->mobile) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">ایمیل (اختیاری)</label>
                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="secondary_phone" class="form-label">شماره ضروری دوم <span class="text-danger">*</span></label>
                            <input id="secondary_phone" name="secondary_phone" class="form-control" required value="{{ old('secondary_phone', $user->secondary_phone) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="postal_code" class="form-label">کد پستی <span class="text-danger">*</span></label>
                            <input id="postal_code" name="postal_code" class="form-control" required value="{{ old('postal_code', $user->postal_code) }}">
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">آدرس پستی <span class="text-danger">*</span></label>
                            <textarea id="address" name="address" rows="4" class="form-control" required>{{ old('address', $user->address) }}</textarea>
                        </div>

                        <div class="col-12 d-grid d-md-flex justify-content-md-end gap-2">
                            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
