<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Domain\Payment\Services\RefundService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $payments = Payment::with(['order.user', 'method', 'bankReceipts'])->latest()->paginate(20);

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

    public function refund(Request $request, Payment $payment, RefundService $refundService)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $refundService->process($payment, $data['amount'], auth()->id(), $data['reason'] ?? null);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'بازپرداخت با موفقیت ثبت شد.');
    }
}
