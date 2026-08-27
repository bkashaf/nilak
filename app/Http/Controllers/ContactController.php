<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Support\PhoneNumberNormalizer;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function showForm()
    {
        // فقط کاربران لاگین اجازه دارند فرم را ببینند
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'برای ارسال پیام باید وارد حساب شوید.');
        }

        // کپچا مثل ثبت‌نام
        $captchaA = random_int(2, 9);
        $captchaB = random_int(1, 9);
        session(['contact_captcha_answer' => $captchaA + $captchaB]);

        return view('themes.admin.pages.blocks.contact-form', [
            'captchaA' => $captchaA,
            'captchaB' => $captchaB,
        ]);
    }

    public function submit(Request $request)
    {
        $captchaAnswer = PhoneNumberNormalizer::normalizeDigits((string) $request->input('captcha_answer', ''));

        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'string', 'max:191'],
            'message' => ['required', 'string', 'max:2000'],
            'captcha_answer' => ['required', 'digits_between:1,3'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $expected = (int) session('contact_captcha_answer', -1);
            $given = (int) PhoneNumberNormalizer::normalizeDigits((string) $request->input('captcha_answer', ''));

            if ($expected < 0 || $given !== $expected) {
                $validator->errors()->add('captcha_answer', 'پاسخ عبارت ریاضی صحیح نیست.');
            }
        });

        $data = $validator->validate();

        ContactMessage::create([
            'user_id' => Auth::id(),
            'subject' => $data['subject'],
            'message' => $data['message'],
        ]);

        session()->forget('contact_captcha_answer');

        return redirect()->back()->with('success', 'پیام شما با موفقیت ارسال شد.');
    }
}
