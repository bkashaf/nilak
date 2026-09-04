<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Domain\Payment\Services\RefundService;
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
        $payments = Payment::with([
            'order.user',
            'method',
            'bankReceipts.reviewer',
            'latestBankReceipt',
        ])->latest()->paginate(20);

        return view('themes.admin.payments.index', compact('payments'));
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,initiated,pending_review,paid,failed,rejected,expired'],
        ]);

        $payment->loadMissing([
            'order',
            'method',
            'latestBankReceipt',
            'bankReceipts',
        ]);

        if ($data['status'] === 'paid') {
            if ($payment->isReceiptPayment() && ! $payment->hasUploadedReceipt()) {
                return back()
                    ->withInput()
                    ->with('error', 'برای ثبت پرداخت‌شده در پرداخت رسیدی، ابتدا باید یک رسید بانکی ثبت شده باشد.');
            }

            app(PaymentStatusService::class)->markPaid($payment, null, null, auth()->id());
        } elseif ($data['status'] === 'pending_review') {
            if ($payment->isReceiptPayment() && ! $payment->hasUploadedReceipt()) {
                return back()
                    ->withInput()
                    ->with('error', 'بدون رسید ثبت‌شده نمی‌توان پرداخت رسیدی را در وضعیت در انتظار بررسی قرار داد.');
            }

            $payment->update([
                'status' => 'pending_review',
                'paid_at' => null,
            ]);
        } elseif (in_array($data['status'], ['failed', 'rejected'], true)) {
            app(PaymentStatusService::class)->markFailed(
                $payment,
                $data['status'],
                array_merge($payment->callback_data ?? [], [
                    'review_source' => 'admin.payments.update',
                ]),
                auth()->id()
            );
        } else {
            $payment->update([
                'status' => $data['status'],
                'paid_at' => null,
            ]);
        }

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'وضعیت پرداخت به‌روزرسانی شد.');
    }

    public function refund(Request $request, Payment $payment, RefundService $refundService)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $refundService->process(
                $payment,
                $data['amount'],
                auth()->id(),
                $data['reason'] ?? null
            );
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'بازپرداخت با موفقیت ثبت شد.');
    }
}