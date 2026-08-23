<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Cart\CartService;
use App\Domain\Order\OrderService;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;

    public function __construct(CartService $cartService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
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

        $data = $request->validate([
            'address' => ['required', 'string', 'min:10', 'max:2000'],
            'payment_method' => ['required', 'string', 'exists:payment_methods,name'],
        ]);

        try {
            $result = $this->orderService->createFromCart(
                Auth::user(),
                $this->cartService,
                $data['address'],
                $data['payment_method'],
            );
        } catch (\Throwable $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('shop.index')->with(
            'success',
            'سفارش شماره ' . $result['order']->id . ' با موفقیت ثبت شد.'
        );
    }
}
