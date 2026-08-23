<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // اگر می‌خواهی همه متدهای این کنترلر فقط برای کاربران احراز هویت‌شده باشد
        // از middleware auth استفاده کن. اگر گروه route قبلاً این را اعمال کرده،
        // می‌توان این خط را حذف یا نگه داشت (تکرار مشکلی ایجاد نمی‌کند).
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(15);
        return view('themes.admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('themes.admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'کاربر با موفقیت ایجاد شد.');
    }

    public function edit(User $user)
    {
        return view('themes.admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        if ($request->filled('password')) {
            $pwdData = $request->validate([
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);

            $data['password'] = Hash::make($pwdData['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'اطلاعات کاربر به‌روزرسانی شد.');
    }

    public function destroy(User $user)
    {
        if (auth()->check() && auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'شما نمی‌توانید حساب خود را حذف کنید.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'کاربر حذف شد.');
    }
}
