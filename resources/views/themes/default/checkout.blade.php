@extends('themes.default.layouts.shop')

@section('title')
تسویه حساب
@endsection

@section('content')
@php
    $cart = session('cart', collect());
    $user = auth()->user();
@endphp

<div class="container">
    <h1>تسویه حساب</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if($cart->isEmpty())
        <p>سبد خرید شما خالی است. ابتدا محصولات را اضافه کنید.</p>
    @else
        <p>تعداد آیتم‌ها: {{ $cart->count() }}</p>
        @guest
            <div class="alert alert-warning">برای ثبت سفارش ابتدا وارد حساب کاربری شوید.</div>
            <a href="{{ route('login') }}" class="btn btn-primary">ورود به حساب</a>
        @else
        <form method="POST" action="{{ route('checkout.process') }}">
            @csrf
            @php
                $hasProfileAddress = filled($user?->address);
                $selectedAddressSource = old('address_source', $hasProfileAddress ? 'profile' : 'new');
            @endphp

            <div class="mb-3">
                <label class="form-label d-block">منبع آدرس تحویل</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="address_source" id="address_source_profile" value="profile" @checked($selectedAddressSource === 'profile') @disabled(!$hasProfileAddress)>
                    <label class="form-check-label" for="address_source_profile">استفاده از آدرس پروفایل</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="address_source" id="address_source_new" value="new" @checked($selectedAddressSource === 'new')>
                    <label class="form-check-label" for="address_source_new">ثبت آدرس جدید</label>
                </div>
                @if(!$hasProfileAddress)
                    <div class="form-text text-warning">در پروفایل شما هنوز آدرسی ثبت نشده است؛ لطفا آدرس جدید وارد کنید.</div>
                @endif
            </div>

            <div id="profileAddressBox" class="mb-3 p-3 border rounded bg-light" style="display: none;">
                <div class="fw-semibold mb-1">آدرس ثبت شده در پروفایل</div>
                <div class="small text-muted">{{ $user?->address ?: 'آدرسی ثبت نشده است.' }}</div>
            </div>

            <div class="mb-3">
                <label for="recipient_name" class="form-label">نام و نام خانوادگی گیرنده</label>
                <input id="recipient_name" name="recipient_name" class="form-control" required
                       value="{{ old('recipient_name', $user?->name) }}">
            </div>
            <div class="mb-3">
                <label for="recipient_mobile" class="form-label">شماره موبایل گیرنده</label>
                <input id="recipient_mobile" name="recipient_mobile" class="form-control" required
                       value="{{ old('recipient_mobile', $user?->mobile) }}">
            </div>
            <div class="mb-3">
                <label for="recipient_phone_alt" class="form-label">شماره ضروری دوم</label>
                <input id="recipient_phone_alt" name="recipient_phone_alt" class="form-control" required
                       value="{{ old('recipient_phone_alt', $user?->secondary_phone) }}">
            </div>
            <div class="mb-3">
                <label for="postal_code" class="form-label">کد پستی (اختیاری)</label>
                <input id="postal_code" name="postal_code" class="form-control"
                       value="{{ old('postal_code', $user?->postal_code) }}">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">آدرس تحویل</label>
                <textarea id="address" name="address" class="form-control" rows="4" data-address-input>{{ old('address', $user?->address) }}</textarea>
            </div>

            <div class="mb-3 form-check">
                <input id="save_address_to_profile" name="save_address_to_profile" type="checkbox" class="form-check-input" value="1" @checked(old('save_address_to_profile'))>
                <label class="form-check-label" for="save_address_to_profile">آدرس و شماره ها در پروفایل ذخیره شود</label>
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">روش پرداخت</label>
                <select id="payment_method" name="payment_method" class="form-select" required>
                    <option value="">انتخاب روش پرداخت</option>
                    @foreach($paymentMethods as $paymentMethod)
                        <option value="{{ $paymentMethod->name }}"
                            data-type="{{ $paymentMethod->type }}"
                            data-config='@json($paymentMethod->config ?? [])'
                            @selected(old('payment_method') === $paymentMethod->name)>
                            {{ $paymentMethod->title }}
                        </option>
                    @endforeach
                </select>
                @if($paymentMethods->isEmpty())
                    <div class="form-text text-danger">هیچ روش پرداخت فعالی تعریف نشده است. لطفا از پنل مدیریت روش پرداخت را فعال کنید.</div>
                @endif
            </div>

            <div id="receiptInfoBox" class="alert alert-info" style="display:none;">
                <div class="fw-semibold mb-2">اطلاعات پرداخت بانکی</div>
                <div id="receiptInfoItems" class="small"></div>
                <div class="small mt-2 text-muted">پس از ثبت سفارش، رسید را در صفحه پیگیری سفارش بارگذاری کنید.</div>
            </div>

            <button type="submit" class="btn btn-primary" @disabled($paymentMethods->isEmpty())>ثبت سفارش و ادامه</button>
        </form>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profileRadio = document.getElementById('address_source_profile');
            const newRadio = document.getElementById('address_source_new');
            const profileAddressBox = document.getElementById('profileAddressBox');
            const addressInput = document.querySelector('[data-address-input]');
            const paymentMethod = document.getElementById('payment_method');
            const receiptInfoBox = document.getElementById('receiptInfoBox');
            const receiptInfoItems = document.getElementById('receiptInfoItems');

            function syncAddressSource() {
                if (!profileRadio || !newRadio || !addressInput) return;

                const useProfile = profileRadio.checked;
                profileAddressBox.style.display = useProfile ? 'block' : 'none';
                addressInput.required = !useProfile;
                addressInput.readOnly = useProfile;

                if (useProfile) {
                    addressInput.classList.add('bg-light');
                } else {
                    addressInput.classList.remove('bg-light');
                }
            }

            function showReceiptInfo() {
                const selected = paymentMethod?.selectedOptions?.[0];
                if (!selected) return;

                const type = selected.dataset.type || '';
                let config = {};
                try {
                    config = JSON.parse(selected.dataset.config || '{}');
                } catch (_) {
                    config = {};
                }

                if (type !== 'receipt') {
                    receiptInfoBox.style.display = 'none';
                    receiptInfoItems.innerHTML = '';
                    return;
                }

                const rows = [];
                if (config.bank_name) rows.push(`بانک: ${config.bank_name}`);
                if (config.account_holder) rows.push(`صاحب حساب: ${config.account_holder}`);
                if (config.card_last4) rows.push(`چهار رقم کارت: ${config.card_last4}`);
                if (config.iban) rows.push(`شماره شبا: ${config.iban}`);
                if (config.note) rows.push(`توضیحات: ${config.note}`);

                receiptInfoItems.innerHTML = rows.length
                    ? rows.map(item => `<div>${item}</div>`).join('')
                    : '<div>برای این روش پرداخت هنوز اطلاعات بانکی در پنل مدیریت ثبت نشده است.</div>';

                receiptInfoBox.style.display = 'block';
            }

            profileRadio?.addEventListener('change', syncAddressSource);
            newRadio?.addEventListener('change', syncAddressSource);
            paymentMethod?.addEventListener('change', showReceiptInfo);

            syncAddressSource();
            showReceiptInfo();
        });
        </script>
        @endguest
    @endif
</div>
@endsection
