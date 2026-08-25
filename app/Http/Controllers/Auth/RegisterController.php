<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('Auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'username' => ['nullable', 'string', 'max:60', 'unique:users,username'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'mobile' => ['required', 'string', 'max:20', 'unique:users,mobile'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
        ], [
            'password.confirmed' => 'تکرار کلمه عبور با کلمه عبور یکسان نیست.',
            'password.min' => 'کلمه عبور باید حداقل 8 کاراکتر باشد.',
            'password.regex' => 'فرمت کلمه عبور درست نیست. کلمه عبور باید شامل حداقل یک حرف انگلیسی و یک عدد باشد.',
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'] ?? null,
            'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'status' => 1,
        ]);

        $customerRole = Role::query()->where('name', 'customer')->first();
        if ($customerRole) {
            $user->roles()->syncWithoutDetaching([$customerRole->id]);
        }

        Auth::login($user);

        return redirect()->route('account.profile.edit')->with('success', 'ثبت نام با موفقیت انجام شد. لطفا اطلاعات پروفایل را تکمیل کنید.');
    }
}
