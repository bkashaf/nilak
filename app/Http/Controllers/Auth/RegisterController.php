<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\PhoneNumberNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    private const DEFAULT_COUNTRY_CODE = '+98';

    public function showRegistrationForm()
    {
        $captchaA = random_int(2, 9);
        $captchaB = random_int(1, 9);

        session([
            'register_captcha_answer' => $captchaA + $captchaB,
        ]);

        return view('Auth.register', [
            'defaultCountryCode' => self::DEFAULT_COUNTRY_CODE,
            'captchaA' => $captchaA,
            'captchaB' => $captchaB,
        ]);
    }

    public function register(Request $request)
    {
        $defaultCountryCode = self::DEFAULT_COUNTRY_CODE;

        $normalizedMobile = PhoneNumberNormalizer::toE164(
            (string) $request->input('country_code', $defaultCountryCode),
            (string) $request->input('mobile', '')
        );
        $captchaAnswer = PhoneNumberNormalizer::normalizeDigits((string) $request->input('captcha_answer', ''));

        $request->merge([
            'mobile' => $normalizedMobile,
            'captcha_answer' => $captchaAnswer,
            'country_code' => $request->input('country_code', $defaultCountryCode),
        ]);

        $validator = Validator::make($request->all(), [
            'country_code' => ['required', 'in:+98,+1'],
            'mobile' => ['required', 'string', 'max:20', 'unique:users,mobile', 'regex:/^[0-9+]{8,20}$/'],
            'email' => ['nullable', 'email', 'max:191', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
            'captcha_answer' => ['required', 'digits_between:1,3'],
        ], [
            'mobile.regex' => 'فرمت شماره موبایل صحیح نیست.',
            'password.confirmed' => 'تکرار کلمه عبور با کلمه عبور یکسان نیست.',
            'password.min' => 'کلمه عبور باید حداقل 8 کاراکتر باشد.',
            'password.regex' => 'کلمه عبور باید شامل حداقل یک حرف انگلیسی بزرگ، یک حرف انگلیسی کوچک و یک عدد باشد.',
            'captcha_answer.required' => 'پاسخ کپچا الزامی است.',
            'captcha_answer.digits_between' => 'پاسخ کپچا معتبر نیست.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $expected = (int) session('register_captcha_answer', -1);
            $given = (int) PhoneNumberNormalizer::normalizeDigits((string) $request->input('captcha_answer', ''));

            if ($expected < 0 || $given !== $expected) {
                $validator->errors()->add('captcha_answer', 'پاسخ عبارت ریاضی صحیح نیست.');
            }
        });

        $data = $validator->validate();

        $user = User::create([
            'name' => $data['mobile'],
            'email' => $data['email'] ?? null,
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'status' => 1,
        ]);

        $customerRole = Role::query()->where('name', 'customer')->first();
        if ($customerRole) {
            $user->roles()->syncWithoutDetaching([$customerRole->id]);
        }

        Auth::login($user);
        session()->forget('register_captcha_answer');

        return redirect()
            ->route('account.profile.edit')
            ->with('success', 'ثبت نام با موفقیت انجام شد.')
            ->with('warning', 'برای فعال شدن خرید، لطفا پروفایل را کامل کنید.');
    }
}
