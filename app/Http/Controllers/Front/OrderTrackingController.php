<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('themes.default.order-tracking');
    }

    public function show(Request $request)
    {
        $data = $request->validate([
            'tracking_code' => ['required', 'string', 'max:100'],
        ]);

        $order = Order::query()
            ->with(['items.product', 'payments.method'])
            ->where('tracking_code', strtoupper(trim($data['tracking_code'])))
            ->first();

        return view('themes.default.order-tracking', [
            'order' => $order,
            'trackingCode' => $data['tracking_code'],
        ]);
    }
}
