<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $paymentMethods = PaymentMethod::query()->orderBy('id')->get();

        return view('themes.admin.payment-methods.index', compact('paymentMethods'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:cod,receipt,gateway'],
            'config' => ['nullable', 'json'],
            'gateway' => ['nullable', 'string', 'max:50'],
            'sandbox_mode' => ['nullable', 'boolean'],
            'merchant_id' => ['nullable', 'string', 'max:255'],
            'callback_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $config = isset($data['config']) ? json_decode($data['config'], true, 512, JSON_THROW_ON_ERROR) : ($paymentMethod->config ?? []);
        if ($request->filled('gateway')) {
            $config['gateway'] = $request->input('gateway');
        }
        if ($request->has('sandbox_mode')) {
            $config['sandbox_mode'] = $request->boolean('sandbox_mode');
        }
        if ($request->filled('merchant_id')) {
            $config['merchant_id'] = $request->input('merchant_id');
        }
        if ($request->filled('callback_url')) {
            $config['callback_url'] = $request->input('callback_url');
        }

        $paymentMethod->update([
            'title' => $data['title'],
            'type' => $data['type'],
            'config' => $config,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'روش پرداخت به‌روزرسانی شد.');
    }
}
