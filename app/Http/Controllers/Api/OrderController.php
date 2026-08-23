<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;

class OrderController extends Controller
{
    /**
     * لیست سفارش‌های کاربر
     */
    public function index(Request $request)
    {
        $orders = Order::with('items')->where('user_id', $request->user_id)->get();
        return response()->json($orders);
    }

    /**
     * ایجاد سفارش جدید
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'items' => 'required|array',
            'payment_method' => 'required|string', // cod, online, bank_receipt
        ]);

        $order = Order::create([
            'user_id' => $request->user_id,
            'total_amount' => 0,
            'status' => 'pending',
        ]);

        $total = 0;

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            $total += $item['price'] * $item['quantity'];
        }

        $order->update(['total_amount' => $total]);

        // 🔹 ایجاد رکورد پرداخت بر اساس روش انتخابی
        $method = PaymentMethod::where('name', $request->payment_method)->firstOrFail();

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $method->id,
            'amount' => $total,
            'status' => $request->payment_method === 'cod'
                ? 'pending'        // پرداخت در محل → پرداخت بعداً انجام می‌شود
                : 'initiated',     // آنلاین یا رسید بانکی → نیاز به ادامهٔ فرآیند
        ]);

        return response()->json([
            'order' => $order->load('items'),
            'payment' => $payment,
        ], 201);
    }

    /**
     * نمایش یک سفارش
     */
    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return response()->json($order);
    }
}


