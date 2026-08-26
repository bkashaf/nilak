<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $pages = Page::query()
            ->published()
            ->orderByDesc('id')
            ->get(['id', 'title', 'slug', 'is_published']);

        $settings = [
            'store_name' => Setting::get('store_name', 'فروشگاه نیلک'),
            'default_locale' => Setting::get('default_locale', 'fa'),
            'currency_label' => Setting::get('currency_label', 'تومان'),
            'tracking_prefix' => Setting::get('tracking_prefix', 'NLK'),
            'default_landing_target' => Setting::get('default_landing_target', 'home'),
            'default_landing_page_id' => Setting::get('default_landing_page_id', ''),
            'sms_provider' => Setting::get('sms_provider', 'none'),
            'sms_sender' => Setting::get('sms_sender', ''),
            'sms_api_key' => Setting::get('sms_api_key', ''),
            'sms_username' => Setting::get('sms_username', ''),
            'sms_password' => Setting::get('sms_password', ''),
            'sms_endpoint' => Setting::get('sms_endpoint', ''),
        ];

        return view('themes.admin.settings.index', compact('settings', 'pages'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:150'],
            'default_locale' => ['required', 'in:fa,en'],
            'currency_label' => ['required', 'string', 'max:30'],
            'tracking_prefix' => ['required', 'alpha_num', 'max:10'],
            'default_landing_target' => ['required', 'in:home,shop,page'],
            'default_landing_page_id' => [
                Rule::requiredIf(fn () => $request->input('default_landing_target') === 'page'),
                'nullable',
                'integer',
                Rule::exists('pages', 'id')->where(fn ($query) => $query->where('is_published', 1)),
            ],
            'sms_provider' => ['required', 'in:none,kavenegar,melipayamak,custom'],
            'sms_sender' => ['nullable', 'string', 'max:50'],
            'sms_api_key' => ['nullable', 'string', 'max:255'],
            'sms_username' => ['nullable', 'string', 'max:100'],
            'sms_password' => ['nullable', 'string', 'max:255'],
            'sms_endpoint' => ['nullable', 'url', 'max:255'],
        ]);

        if (($data['default_landing_target'] ?? 'home') !== 'page') {
            $data['default_landing_page_id'] = null;
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'تنظیمات ذخیره شد.');
    }
}
