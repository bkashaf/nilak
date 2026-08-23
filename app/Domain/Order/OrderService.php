<?php

namespace App\Domain\Order;

use App\Domain\Cart\CartService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function createFromCart(User $user, CartService $cart, string $address, string $paymentMethodName): array
    {
        $cartItems = $cart->items();

        if ($cartItems->isEmpty()) {
            throw new \InvalidArgumentException('سبد خرید خالی است.');
        }

        $items = $cartItems->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
        ])->all();

        $result = $this->createFromItems($user, $items, $address, $paymentMethodName);
        $cart->clear();

        return $result;
    }

    public function createFromItems(User $user, array $items, string $address, string $paymentMethodName): array
    {
        return DB::transaction(function () use ($user, $items, $address, $paymentMethodName) {
            $paymentMethod = PaymentMethod::query()
                ->where('name', $paymentMethodName)
                ->where('is_active', true)
                ->firstOrFail();

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'status' => 'pending',
                'tracking_code' => $this->generateTrackingCode(),
                'address' => $address,
            ]);

            $total = 0;

            foreach ($items as $item) {
                $product = ! empty($item['product_id'])
                    ? Product::query()->lockForUpdate()->find($item['product_id'])
                    : null;

                if (! $product || ! $product->is_active) {
                    throw new \RuntimeException('یکی از محصولات سبد دیگر قابل سفارش نیست.');
                }

                if ($product->stock < $item['quantity']) {
                    throw new \RuntimeException("موجودی محصول «{$product->name}» کافی نیست.");
                }

                $quantity = max(1, (int) $item['quantity']);
                $unitPrice = (int) $product->price;
                $itemTotal = $unitPrice * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $unitPrice,
                    'total' => $itemTotal,
                ]);

                $product->decrement('stock', $quantity);
                $total += $itemTotal;
            }

            $order->update(['total_amount' => $total]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'amount' => $total,
                'status' => $paymentMethod->type === 'cod' ? 'pending' : 'initiated',
                'gateway_name' => $paymentMethod->type === 'gateway'
                    ? ($paymentMethod->config['gateway'] ?? $paymentMethod->name)
                    : null,
            ]);

            return [
                'order' => $order->load('items'),
                'payment' => $payment->load('method'),
            ];
        });
    }

    private function generateTrackingCode(): string
    {
        do {
            $code = 'NLK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Order::where('tracking_code', $code)->exists());

        return $code;
    }
}
