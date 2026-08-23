<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Domain\Order\OrderService;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

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

        $result = $this->orderService->createFromItems(
            $request->user(),
            $request->items,
            $request->address,
            $request->payment_method,
        );

        return response()->json([
            'order' => $result['order'],
            'payment' => $result['payment'],
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

}


