<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $payments = Payment::with(['order.user', 'method'])->latest()->paginate(20);

        return view('themes.admin.payments.index', compact('payments'));
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,initiated,pending_review,paid,failed,rejected,expired'],
        ]);

        if ($data['status'] === 'paid') {
            app(PaymentStatusService::class)->markPaid($payment, null, null, auth()->id());
        } elseif (in_array($data['status'], ['failed', 'rejected'], true)) {
            app(PaymentStatusService::class)->markFailed($payment, $data['status'], null, auth()->id());
        } else {
            $payment->update(['status' => $data['status'], 'paid_at' => null]);
        }

        return redirect()->route('admin.payments.index')->with('success', 'وضعیت پرداخت به‌روزرسانی شد.');
    }
}
