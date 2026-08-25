<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index(Request $request)
    {
        $trackingCode = strtoupper(trim((string) $request->query('tracking_code', '')));
        $order = null;

        if ($trackingCode !== '') {
            $order = Order::query()
                ->with(['items.product', 'payments.method', 'statusHistories', 'payments.statusHistories'])
                ->where('tracking_code', $trackingCode)
                ->first();
        }

        return view('themes.default.order-tracking', [
            'order' => $order,
            'trackingCode' => $trackingCode,
        ]);
    }

    public function show(Request $request)
    {
        $data = $request->validate([
            'tracking_code' => ['required', 'string', 'max:100'],
        ]);

        $order = Order::query()
            ->with(['items.product', 'payments.method', 'statusHistories', 'payments.statusHistories'])
            ->where('tracking_code', strtoupper(trim($data['tracking_code'])))
            ->first();

        return view('themes.default.order-tracking', [
            'order' => $order,
            'trackingCode' => $data['tracking_code'],
        ]);
    }
}
