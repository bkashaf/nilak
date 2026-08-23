<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'فروشگاه نیلک'),
            'default_locale' => Setting::get('default_locale', 'fa'),
            'currency_label' => Setting::get('currency_label', 'تومان'),
            'tracking_prefix' => Setting::get('tracking_prefix', 'NLK'),
        ];

        return view('themes.admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:150'],
            'default_locale' => ['required', 'in:fa,en'],
            'currency_label' => ['required', 'string', 'max:30'],
            'tracking_prefix' => ['required', 'alpha_num', 'max:10'],
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'تنظیمات ذخیره شد.');
    }
}
