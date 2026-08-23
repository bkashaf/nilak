<?php

namespace App\Domain\Order;

use App\Domain\Cart\CartService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createFromCart(User $user, CartService $cart, string $address, string $paymentMethodName): array
    {
        $cartItems = $cart->items();

        if ($cartItems->isEmpty()) {
            throw new \InvalidArgumentException('سبد خرید خالی است.');
        }

        return DB::transaction(function () use ($user, $cart, $cartItems, $address, $paymentMethodName) {
            $paymentMethod = PaymentMethod::query()
                ->where('name', $paymentMethodName)
                ->where('is_active', true)
                ->firstOrFail();

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'status' => 'pending',
                'address' => $address,
            ]);

            $total = 0;

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product_id
                    ? \App\Models\Product::query()->lockForUpdate()->find($cartItem->product_id)
                    : null;

                if (! $product || ! $product->is_active) {
                    throw new \RuntimeException('یکی از محصولات سبد دیگر قابل سفارش نیست.');
                }

                if ($product->stock < $cartItem->quantity) {
                    throw new \RuntimeException("موجودی محصول «{$product->name}» کافی نیست.");
                }

                $quantity = max(1, (int) $cartItem->quantity);
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
                'gateway_name' => $paymentMethod->type === 'gateway' ? $paymentMethod->name : null,
            ]);

            $cart->clear();

            return [
                'order' => $order->load('items'),
                'payment' => $payment->load('method'),
            ];
        });
    }
}
