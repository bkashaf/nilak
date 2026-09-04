<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $orders = Order::query()
            ->with([
                'user',
                'payments.method',
                'payments.latestBankReceipt',
            ])
            ->latest()
            ->paginate(20);

        return view('themes.admin.orders.index', compact('orders'));
    }

    public function edit(Order $order)
    {
        $order->load([
            'user',
            'items.product',
            'payments.method',
            'payments.latestBankReceipt',
            'payments.bankReceipts.reviewer',
            'payments.bankReceipts.uploader',
        ]);

        $payment = $order->payments->sortByDesc('id')->first();
        $latestReceipt = $payment?->latestBankReceipt;

        return view('themes.admin.orders.edit', compact('order', 'payment', 'latestReceipt'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,paid,canceled,shipped,delivered'],
            'payment_status' => ['required', 'string', 'in:pending,initiated,pending_review,paid,failed,rejected,expired'],
            'tracking_code' => ['nullable', 'string', 'max:100'],
        ]);

        $oldStatus = $order->status;

        $order->update([
            'status' => $data['status'],
            'tracking_code' => $data['tracking_code'],
        ]);

        if ($oldStatus !== $data['status']) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $oldStatus,
                'to_status' => $data['status'],
                'changed_by' => auth()->id(),
            ]);
        }

        $payment = $order->payments()->with([
            'method',
            'latestBankReceipt',
            'bankReceipts',
        ])->latest('id')->first();

        if ($payment) {
            if ($data['status'] === 'delivered' && $payment->method?->type === 'cod') {
                app(PaymentStatusService::class)->markPaid($payment, null, null, auth()->id());
            } elseif ($data['payment_status'] === 'paid') {
                if ($payment->isReceiptPayment() && ! $payment->hasUploadedReceipt()) {
                    return back()
                        ->withInput()
                        ->with('error', 'برای ثبت پرداخت‌شده در پرداخت رسیدی، ابتدا باید یک رسید بانکی ثبت شده باشد.');
                }

                app(PaymentStatusService::class)->markPaid($payment, null, null, auth()->id());
            } elseif ($data['payment_status'] === 'pending_review') {
                if ($payment->isReceiptPayment() && ! $payment->hasUploadedReceipt()) {
                    return back()
                        ->withInput()
                        ->with('error', 'بدون رسید ثبت‌شده نمی‌توان پرداخت رسیدی را در وضعیت در انتظار بررسی قرار داد.');
                }

                $payment->update([
                    'status' => 'pending_review',
                    'paid_at' => null,
                ]);
            } elseif (in_array($data['payment_status'], ['failed', 'rejected'], true)) {
                app(PaymentStatusService::class)->markFailed(
                    $payment,
                    $data['payment_status'],
                    array_merge($payment->callback_data ?? [], [
                        'review_source' => 'admin.orders.update',
                    ]),
                    auth()->id()
                );
            } else {
                $payment->update([
                    'status' => $data['payment_status'],
                    'paid_at' => null,
                ]);
            }
        }

        return redirect()
            ->route('admin.orders.edit', $order)
            ->with('success', 'وضعیت سفارش و شماره پیگیری به‌روزرسانی شد.');
    }
}