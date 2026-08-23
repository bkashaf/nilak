<?php

namespace App\Http\Controllers\Front;

use App\Domain\Payment\Services\PaymentService;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function zarinpal(Request $request, Payment $payment, PaymentService $paymentService)
    {
        try {
            $result = $paymentService->verify($payment, $request->all());

            return redirect()->route('orders.track.form')->with(
                $result['status'] === 'paid' ? 'success' : 'error',
                $result['status'] === 'paid' ? 'پرداخت با موفقیت تأیید شد.' : 'تأیید پرداخت ناموفق بود.'
            );
        } catch (\Throwable $exception) {
            return redirect()->route('orders.track.form')->with('error', 'خطا در تأیید پرداخت.');
        }
    }
}
