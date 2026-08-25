<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Cart\CartService;
use App\Domain\Order\OrderService;
use App\Domain\Payment\Services\PaymentService;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;
    protected PaymentService $paymentService;

    public function __construct(CartService $cartService, OrderService $orderService, PaymentService $paymentService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    /**
     * نمایش صفحه سبد خرید
     */
    public function index()
{
    $cart = $this->cartService;
    $items = $cart->items();
    $total = $cart->total();

        // سبد عملیاتی: نمایش جزئیات، جمع و مسیر تسویه
        return view('themes.cart', compact('cart', 'items', 'total'));
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
            'address_source' => ['required', 'in:profile,new'],
            'address' => ['nullable', 'string', 'min:10', 'max:2000', 'required_if:address_source,new'],
            'payment_method' => ['required', 'string', 'exists:payment_methods,name'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'recipient_mobile' => ['required', 'string', 'max:20'],
            'recipient_phone_alt' => ['required', 'string', 'max:20'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'save_address_to_profile' => ['nullable', 'boolean'],
        ]);

        $user = Auth::user();
        $address = $data['address_source'] === 'profile'
            ? trim((string) $user?->address)
            : trim((string) ($data['address'] ?? ''));

        if ($address === '') {
            return redirect()->back()->withInput()->withErrors([
                'address' => 'برای ادامه، آدرس تحویل را از پروفایل انتخاب کنید یا آدرس جدید وارد کنید.',
            ]);
        }

        try {
            $deliveryDetails = [
                'recipient_name' => $data['recipient_name'],
                'recipient_mobile' => $data['recipient_mobile'],
                'recipient_phone_alt' => $data['recipient_phone_alt'],
                'postal_code' => $data['postal_code'] ?? null,
            ];

            if ($request->boolean('save_address_to_profile') && $user) {
                $user->update([
                    'mobile' => $data['recipient_mobile'],
                    'secondary_phone' => $data['recipient_phone_alt'],
                    'postal_code' => $data['postal_code'] ?? null,
                    'address' => $address,
                    'name' => $data['recipient_name'],
                ]);
            }

            $result = $this->orderService->createFromCart(
                $user,
                $this->cartService,
                $address,
                $data['payment_method'],
                null,
                $deliveryDetails,
            );
        } catch (\Throwable $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        $order = $result['order'];
        $payment = $result['payment']->load('method');
        $methodType = $payment->method?->type;

        if ($methodType === 'gateway') {
            try {
                $initiation = $this->paymentService->initiate($payment->load('order.user'));

                if (! empty($initiation['redirect_url'])) {
                    return redirect()->away($initiation['redirect_url']);
                }

                return redirect()
                    ->route('orders.track.form', ['tracking_code' => $order->tracking_code])
                    ->with('warning', 'سفارش ثبت شد، اما لینک انتقال به درگاه در دسترس نیست.');
            } catch (\Throwable $exception) {
                return redirect()
                    ->route('orders.track.form', ['tracking_code' => $order->tracking_code])
                    ->with('error', 'سفارش ثبت شد، اما شروع پرداخت آنلاین ناموفق بود: ' . $exception->getMessage());
            }
        }

        if ($methodType === 'receipt') {
            return redirect()
                ->route('orders.track.form', ['tracking_code' => $order->tracking_code])
                ->with('success', 'سفارش ثبت شد. لطفا رسید بانکی را از بخش پیگیری سفارش ارسال کنید.');
        }

        return redirect()
            ->route('orders.track.form', ['tracking_code' => $order->tracking_code])
            ->with('success', 'سفارش شماره ' . $order->tracking_code . ' با موفقیت ثبت شد.');
    }
}
