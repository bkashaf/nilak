<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * دریافت سبد خرید از سشن
     */
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
        return response()->json($cart);
    }

    /**
     * افزودن محصول به سبد خرید
     */
    public function add(Request $request)
    {
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return response()->json(['message' => 'added', 'cart' => $cart]);
    }

    /**
     * حذف یک محصول از سبد خرید
     */
    public function remove(Request $request)
    {
        $productId = $request->product_id;

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return response()->json(['message' => 'removed', 'cart' => $cart]);
    }

    /**
     * خالی کردن کامل سبد خرید
     */
    public function clear()
    {
        session()->forget('cart');
        return response()->json(['message' => 'cleared']);
    }
}
