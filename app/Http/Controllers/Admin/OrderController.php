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

        return view('themes.admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,paid,canceled,shipped,delivered'],
            'tracking_code' => ['nullable', 'string', 'max:100'],
        ]);

        $order->update($data);

        return redirect()
            ->route('admin.orders.edit', $order)
            ->with('success', 'وضعیت سفارش و شماره پیگیری به‌روزرسانی شد.');
    }
}
