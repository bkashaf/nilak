<?php

namespace App\Domain\Inventory;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryReservationService
{
    public function reserve(Order $order): void
    {
        if ($order->inventory_status !== 'none') {
            return;
        }

        foreach ($order->items as $item) {
            $product = Product::query()->lockForUpdate()->find($item->product_id);

            if (! $product || ! $product->is_active) {
                throw new RuntimeException('یکی از محصولات سفارش دیگر قابل فروش نیست.');
            }

            $available = $product->stock - (int) $product->reserved_stock;
            if ($available < $item->quantity) {
                throw new RuntimeException("موجودی قابل فروش محصول «{$product->name}» کافی نیست.");
            }

            $product->increment('reserved_stock', $item->quantity);
        }

        $order->update(['inventory_status' => 'reserved']);
    }

    public function commit(Order $order): void
    {
        if ($order->inventory_status !== 'reserved') {
            return;
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::query()->lockForUpdate()->find($item->product_id);
                if (! $product) {
                    continue;
                }

                $quantity = min((int) $item->quantity, (int) $product->reserved_stock);
                $product->decrement('reserved_stock', $quantity);
                $product->decrement('stock', $quantity);
            }

            $order->update(['inventory_status' => 'committed']);
        });
    }

    public function release(Order $order): void
    {
        if ($order->inventory_status !== 'reserved') {
            return;
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::query()->lockForUpdate()->find($item->product_id);
                if (! $product) {
                    continue;
                }

                $quantity = min((int) $item->quantity, (int) $product->reserved_stock);
                $product->decrement('reserved_stock', $quantity);
            }

            $order->update(['inventory_status' => 'released']);
        });
    }
}
