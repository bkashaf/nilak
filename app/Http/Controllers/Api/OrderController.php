<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * لیست سفارش‌های کاربر
     */
    public function index(Request $request)
    {
        $orders = Order::with('items')->where('user_id', $request->user()->id)->get();
        return response()->json($orders);
    }

    /**
     * ایجاد سفارش جدید
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'address' => 'required|string|min:10|max:2000',
            'payment_method' => 'required|string', // cod, online, bank_receipt
        ]);

        $order = DB::transaction(function () use ($request) {
            $method = PaymentMethod::where('name', $request->payment_method)
                ->where('is_active', true)
                ->firstOrFail();
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => 0,
                'status' => 'pending',
                'tracking_code' => $this->trackingCode(),
                'address' => $request->address,
            ]);
            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                abort_if(! $product->is_active || $product->stock < $item['quantity'], 422, 'محصول قابل سفارش نیست یا موجودی کافی ندارد.');
                $price = (int) $product->price;
                $quantity = (int) $item['quantity'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $price * $quantity,
                ]);
                $product->decrement('stock', $quantity);
                $total += $price * $quantity;
            }
            $order->update(['total_amount' => $total]);
            Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $method->id,
                'amount' => $total,
                'status' => $method->type === 'cod' ? 'pending' : 'initiated',
                'gateway_name' => $method->type === 'gateway' ? ($method->config['gateway'] ?? $method->name) : null,
            ]);
            return $order->load('items', 'payments');
        });

        return response()->json([
            'order' => $order->load('items'),
            'payment' => $order->payments->last(),
        ], 201);
    }

    /**
     * نمایش یک سفارش
     */
    public function show(Request $request, $id)
    {
        $order = Order::with('items')->where('user_id', $request->user()->id)->findOrFail($id);
        return response()->json($order);
    }

    private function trackingCode(): string
    {
        do {
            $code = 'NLK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Order::where('tracking_code', $code)->exists());
        return $code;
    }
}


