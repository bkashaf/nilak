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
            ->with([
                'payments.method',
                'payments.latestBankReceipt',
            ])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return view('themes.default.account.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'دسترسی غیرمجاز به سفارش دیگران');
        }

        $order->load([
            'payments.method',
            'payments.latestBankReceipt',
            'payments.bankReceipts.reviewer',
            'payments.bankReceipts.uploader',
            'items.product',
        ]);

        return view('themes.default.account.order-show', compact('order'));
    }

    public function uploadReceipt(Request $request, Payment $payment)
    {
        $payment->loadMissing([
            'order',
            'method',
            'latestBankReceipt',
            'bankReceipts',
        ]);

        if ($payment->order->user_id !== auth()->id()) {
            abort(403, 'دسترسی غیرمجاز به این پرداخت');
        }

        if (! $payment->canUploadReceipt()) {
            return back()->with('error', 'این پرداخت در وضعیت قابل ارسال رسید نیست.');
        }

        $data = $request->validate([
            'tracking_number' => ['required', 'string', 'max:100'],
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $file = $request->file('receipt');
        $path = $file->store('receipts', 'public');

        BankReceipt::create([
            'payment_id' => $payment->id,
            'tracking_number' => $data['tracking_number'],
            'note' => $data['note'] ?? null,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now(),
            'status' => 'pending_review',
        ]);

        $payment->update([
            'status' => 'pending_review',
            'callback_data' => array_merge($payment->callback_data ?? [], [
                'tracking_number' => $data['tracking_number'],
                'receipt_path' => $path,
                'receipt_original_name' => $file->getClientOriginalName(),
                'receipt_note' => $data['note'] ?? null,
                'uploaded_at' => now()->toDateTimeString(),
            ]),
        ]);

        return back()->with('success', 'رسید بانکی با موفقیت ثبت شد و در انتظار بررسی مدیر است.');
    }
}