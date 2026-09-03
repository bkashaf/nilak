<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BankReceipt;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['payments.method'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return view('themes.default.account.orders', compact('orders'));
    }

    // ✅ متد جدید برای نمایش جزئیات سفارش
    public function show(Order $order)
    {
        // بررسی اینکه سفارش متعلق به کاربر فعلی است
        if ($order->user_id !== auth()->id()) {
            abort(403, 'دسترسی غیرمجاز به سفارش دیگران');
        }

        // بارگذاری پرداخت‌ها و جزئیات مرتبط
        $order->load(['payments.method', 'items.product', 'payments.bankReceipts']);

        return view('themes.default.account.order-show', compact('order'));
    }

    public function uploadReceipt(Request $request, Payment $payment)
    {
        if ($payment->order->user_id !== auth()->id()) {
            abort(403, 'دسترسی غیرمجاز به این پرداخت');
        }

        if ($payment->method?->type !== 'receipt' || $payment->status !== 'initiated') {
            return back()->with('error', 'این پرداخت در وضعیت قابل ارسال رسید نیست.');
        }

        $data = $request->validate([
            'tracking_number' => ['required', 'string', 'max:100'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $path = $request->hasFile('receipt')
            ? $request->file('receipt')->store('receipts', 'public')
            : null;

        $payment->update([
            'callback_data' => array_merge($payment->callback_data ?? [], [
                'tracking_number' => $data['tracking_number'],
                'note' => $data['note'] ?? null,
                'receipt_path' => $path,
                'uploaded_at' => now()->toDateTimeString(),
            ]),
            'status' => 'pending_review',
        ]);

        BankReceipt::create([
            'payment_id' => $payment->id,
            'file_path' => $path,
            'status' => 'pending_review',
        ]);

        return back()->with('success', 'رسید بانکی با موفقیت ثبت شد و در انتظار بررسی است.');
    }
}
