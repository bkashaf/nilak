<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PhoneNumberNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('Auth.login', [
            'defaultCountryCode' => app()->getLocale() === 'fa' ? '+98' : '+1',
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'country_code' => ['required', 'in:+98,+1'],
            'mobile'    => ['required', 'string', 'max:20'],
            'password' => ['required'],
        ]);

        $variants = PhoneNumberNormalizer::variants($data['mobile'], $data['country_code']);
        $user = User::query()->whereIn('mobile', $variants)->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if (! $user->isProfileComplete()) {
                $request->session()->flash('warning', 'برای فعال شدن خرید، لطفا پروفایل را کامل کنید.');
            }

            if ($user && $user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'mobile' => 'اطلاعات ورود نادرست است.',
        ])->onlyInput('mobile', 'country_code');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
