<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ContactMessage::create([
            'name'    => $data['name'],
            'email'   => $data['email'],
            'message' => $data['message'],
        ]);

        return back()->with('success', 'پیام شما با موفقیت ارسال شد.');
    }
}
