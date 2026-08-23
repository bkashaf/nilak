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
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $paymentMethod->update([
            'title' => $data['title'],
            'type' => $data['type'],
            'config' => isset($data['config']) ? json_decode($data['config'], true, 512, JSON_THROW_ON_ERROR) : null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'روش پرداخت به‌روزرسانی شد.');
    }
}
