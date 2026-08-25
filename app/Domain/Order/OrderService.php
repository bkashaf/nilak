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
    public function createFromCart(User $user, CartService $cart, string $address, string $paymentMethodName, ?string $idempotencyKey = null, ?array $deliveryDetails = null): array
    {
        $cartItems = $cart->items();

        if ($cartItems->isEmpty()) {
            throw new \InvalidArgumentException('سبد خرید خالی است.');
        }

        $items = $cartItems->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
        ])->all();

        $result = $this->createFromItems($user, $items, $address, $paymentMethodName, $idempotencyKey, $deliveryDetails);
        $cart->clear();

        return $result;
    }

    public function createFromItems(User $user, array $items, string $address, string $paymentMethodName, ?string $idempotencyKey = null, ?array $deliveryDetails = null): array
    {
        if ($idempotencyKey) {
            $existing = Payment::where('idempotency_key', $idempotencyKey)->with(['order.items', 'method'])->first();
            if ($existing) {
                return ['order' => $existing->order, 'payment' => $existing];
            }
        }

        return DB::transaction(function () use ($user, $items, $address, $paymentMethodName, $idempotencyKey, $deliveryDetails) {
            $paymentMethod = PaymentMethod::query()
                ->where('name', $paymentMethodName)
                ->where('is_active', true)
                ->firstOrFail();

            $recipientName = trim((string) ($deliveryDetails['recipient_name'] ?? $user->name ?? ''));
            $recipientMobile = trim((string) ($deliveryDetails['recipient_mobile'] ?? $user->mobile ?? ''));
            $recipientPhoneAlt = trim((string) ($deliveryDetails['recipient_phone_alt'] ?? $user->secondary_phone ?? ''));

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'status' => 'pending',
                'inventory_status' => 'none',
                'tracking_code' => $this->generateTrackingCode(),
                'address' => $address,
                'recipient_name' => $recipientName ?: $user->name,
                'recipient_mobile' => $recipientMobile ?: $user->mobile,
                'recipient_phone_alt' => $recipientPhoneAlt ?: null,
                'postal_code' => $deliveryDetails['postal_code'] ?? null,
            ]);

            $total = 0;

            foreach ($items as $item) {
                $product = ! empty($item['product_id'])
                    ? Product::query()->lockForUpdate()->find($item['product_id'])
                    : null;

                if (! $product || ! $product->is_active) {
                    throw new \RuntimeException('یکی از محصولات سبد دیگر قابل سفارش نیست.');
                }

                if (($product->stock - (int) $product->reserved_stock) < $item['quantity']) {
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

                $total += $itemTotal;
            }

            app(\App\Domain\Inventory\InventoryReservationService::class)->reserve($order->load('items'));

            $order->update(['total_amount' => $total]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'amount' => $total,
                'status' => $paymentMethod->type === 'cod' ? 'pending' : 'initiated',
                'idempotency_key' => $idempotencyKey,
                'expires_at' => $paymentMethod->type === 'gateway' ? now()->addMinutes((int) config('payment.expiration_minutes', 30)) : null,
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
