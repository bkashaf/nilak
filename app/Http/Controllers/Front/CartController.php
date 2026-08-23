<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Cart\CartService;
use App\Models\Product;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * نمایش صفحه سبد خرید
     */
    public function index()
{
    $cart = $this->cartService;
    $items = $cart->items();
    $total = $cart->total();

    // فقط از نسخهٔ جدید در مسیر themes/default استفاده شود
    return view('themes.default.cart', compact('items', 'total'));
}

    /**
     * افزودن محصول به سبد خرید
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1'
        ]);

        $product = Product::find($request->input('product_id'));
        if (! $product || ! $product->is_active) {
            return redirect()->back()->with('error', 'محصول نامعتبر است.');
        }

        $qty = (int) $request->input('qty', 1);
        $this->cartService->add($product->id, $qty);

        return redirect()->back()->with('success', 'محصول به سبد اضافه شد.');
    }

    /**
     * بروزرسانی تعداد محصول در سبد
     */
    public function update(Request $request, $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $this->cartService->update((int)$productId, (int)$request->input('quantity'));
        return redirect()->back()->with('success', 'تعداد به‌روزرسانی شد.');
    }

    /**
     * حذف محصول از سبد
     */
    public function remove(Request $request, $productId)
    {
        $this->cartService->remove((int)$productId);
        return redirect()->back()->with('success', 'آیتم حذف شد.');
    }

    /**
     * تسویه حساب (checkout)
     */
    public function checkout(Request $request)
    {
        $cart = $this->cartService;
        if ($cart->all()->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'سبد خرید خالی است.');
        }

        // اینجا منطق ایجاد سفارش یا پرداخت قرار می‌گیرد
        $this->cartService->clear();

        return redirect()->route('shop.index')->with('success', 'سفارش ثبت شد (نمونه).');
    }
}
