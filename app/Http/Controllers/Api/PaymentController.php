<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Domain\Payment\Services\PaymentService;

class PaymentController extends Controller
{
    protected PaymentService $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    /**
     * شروع پرداخت
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::findOrFail($request->payment_id);

        $result = $this->service->initiate($payment);

        return response()->json($result);
    }

    /**
     * تأیید پرداخت (callback بانک)
     */
    public function verify(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'callback_data' => 'required|array',
        ]);

        $payment = Payment::findOrFail($request->payment_id);

        $result = $this->service->verify($payment, $request->callback_data);

        return response()->json($result);
    }
}
