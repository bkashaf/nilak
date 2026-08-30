<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    /**
     * نمایش فرم تماس (در صورت نیاز)
     */
    public function showForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'برای ارسال پیام باید وارد حساب شوید.');
        }

        return view('themes.admin.pages.blocks.contact-form');
    }

    /**
     * دریافت پیام از فرم تماس TinyMCE
     */
    public function submit(Request $request)
    {
        // فقط کاربران لاگین‌شده اجازه ارسال دارند
        $user = Auth::user();

        // اعتبارسنجی فرم TinyMCE
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'name'    => ['nullable', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
        ]);

        // ذخیره پیام در دیتابیس
        ContactMessage::create([
            'user_id' => $user ? $user->id : null,
            'subject' => '', // فرم TinyMCE subject ندارد
            'message' => $validated['message'],
        ]);

        // پیام موفقیت
        return back()->with('success', 'پیام شما با موفقیت ارسال شد.');
    }
}
