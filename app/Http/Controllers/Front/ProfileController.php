<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('themes.default.account.profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'username' => ['nullable', 'string', 'max:60', 'unique:users,username,' . $user->id],
            'mobile' => ['required', 'string', 'max:20', 'unique:users,mobile,' . $user->id],
            'secondary_phone' => ['required', 'string', 'max:20'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'] ?? null,
            'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'mobile' => $data['mobile'],
            'secondary_phone' => $data['secondary_phone'],
            'postal_code' => $data['postal_code'] ?? null,
            'address' => $data['address'],
        ]);

        return redirect()->route('account.profile.edit')->with('success', 'پروفایل با موفقیت به روز شد.');
    }
}
