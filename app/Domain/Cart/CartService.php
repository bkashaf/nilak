<?php

namespace App\Domain\Cart;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    protected const SESSION_KEY = 'cart';

    /**
     * بازگرداندن کل سبد به صورت Collection
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return collect(session(self::SESSION_KEY, []));
    }

    /**
     * افزودن یا افزایش تعداد یک محصول
     *
     * @param int $productId
     * @param int $qty
     * @return Collection
     */
    public function add(int $productId, int $qty = 1): Collection
    {
        $cart = $this->all();
        $key = (string) $productId;

        if ($cart->has($key)) {
            $item = $cart->get($key);
            $item['quantity'] = ($item['quantity'] ?? 0) + $qty;
            $cart->put($key, $item);
        } else {
            $product = Product::find($productId);
            $cart->put($key, [
                'product_id' => $productId,
                'product' => $product,
                'price' => $product ? $product->price : 0,
                'quantity' => $qty,
            ]);
        }

        session([self::SESSION_KEY => $cart]);

        return $cart;
    }

    /**
     * به‌روزرسانی تعداد یک آیتم
     *
     * @param int $productId
     * @param int $qty
     * @return Collection
     */
    public function update(int $productId, int $qty): Collection
    {
        $cart = $this->all();
        $key = (string) $productId;

        if ($cart->has($key)) {
            $item = $cart->get($key);
            $item['quantity'] = max(1, $qty);
            $cart->put($key, $item);
            session([self::SESSION_KEY => $cart]);
        }

        return $cart;
    }

    /**
     * حذف یک آیتم
     *
     * @param int $productId
     * @return Collection
     */
    public function remove(int $productId): Collection
    {
        $cart = $this->all();
        $key = (string) $productId;

        if ($cart->has($key)) {
            $cart->forget($key);
            session([self::SESSION_KEY => $cart]);
        }

        return $cart;
    }

    /**
     * پاک کردن کامل سبد
     *
     * @return void
     */
    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * محاسبه مجموع قیمت
     *
     * @return float
     */
    public function total(): float
    {
        return $this->all()->reduce(function ($carry, $item) {
            $price = $item['price'] ?? 0;
            $qty = $item['quantity'] ?? ($item['qty'] ?? 0);
            return $carry + ($price * $qty);
        }, 0.0);
    }

    /**
     * تبدیل به ساختار کمکی برای view (items با فیلدهای مورد نیاز)
     *
     * @return \Illuminate\Support\Collection
     */
    public function items(): Collection
    {
        return $this->all()->map(function ($item) {
            $product = $item['product'] ?? null;
            $quantity = $item['quantity'] ?? ($item['qty'] ?? 0);
            $price = $item['price'] ?? 0;
            return (object) [
                'product_id' => $item['product_id'] ?? ($product->id ?? null),
                'product' => $product,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $price * $quantity,
            ];
        })->values();
    }
}
