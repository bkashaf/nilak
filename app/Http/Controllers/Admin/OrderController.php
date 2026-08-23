<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
            ->with(['user', 'payments.method'])
            ->latest()
            ->paginate(20);

        return view('themes.admin.orders.index', compact('orders'));
    }

    public function edit(Order $order)
    {
        $order->load(['user', 'items.product', 'payments.method']);
        $payment = $order->payments->sortByDesc('id')->first();

        return view('themes.admin.orders.edit', compact('order', 'payment'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,paid,canceled,shipped,delivered'],
            'payment_status' => ['required', 'string', 'in:pending,initiated,pending_review,paid,failed,rejected'],
            'tracking_code' => ['nullable', 'string', 'max:100'],
        ]);

        $order->update([
            'status' => $data['status'],
            'tracking_code' => $data['tracking_code'],
        ]);

        $payment = $order->payments()->latest('id')->first();
        if ($payment) {
            $payment->update([
                'status' => $data['payment_status'],
                'paid_at' => $data['payment_status'] === 'paid' ? ($payment->paid_at ?? now()) : null,
            ]);
        }

        return redirect()
            ->route('admin.orders.edit', $order)
            ->with('success', 'وضعیت سفارش و شماره پیگیری به‌روزرسانی شد.');
    }
}
