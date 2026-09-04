<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Http\Controllers\Controller;
use App\Models\BankReceipt;
use Illuminate\Http\Request;

class BankReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function show(BankReceipt $bankReceipt)
    {
        $bankReceipt->load([
            'payment.order.user',
            'payment.method',
            'payment.latestBankReceipt',
            'uploader',
            'reviewer',
        ]);

        return view('themes.admin.payments.show', compact('bankReceipt'));
    }

    public function approve(Request $request, BankReceipt $bankReceipt)
    {
        $bankReceipt->loadMissing([
            'payment.order',
            'payment.method',
            'payment.latestBankReceipt',
        ]);

        $payment = $bankReceipt->payment;

        if (! $payment || ! $payment->isReceiptPayment()) {
            return back()->with('error', 'این پرداخت از نوع رسید بانکی نیست.');
        }

        if (! $payment->isUnderReceiptReview()) {
            return back()->with('error', 'این پرداخت در وضعیت قابل بررسی نیست.');
        }

        if (! $payment->latestBankReceipt || $payment->latestBankReceipt->id !== $bankReceipt->id) {
            return back()->with('error', 'فقط آخرین رسید ثبت‌شده قابل بررسی است.');
        }

        if ($bankReceipt->status !== 'pending_review') {
            return back()->with('error', 'این رسید قبلاً بررسی شده است.');
        }

        app(PaymentStatusService::class)->markPaid($payment, null, null, auth()->id());

        $bankReceipt->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('admin.bank-receipts.show', $bankReceipt)
            ->with('success', 'رسید بانکی تأیید شد.');
    }

    public function reject(Request $request, BankReceipt $bankReceipt)
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $bankReceipt->loadMissing([
            'payment.order',
            'payment.method',
            'payment.latestBankReceipt',
        ]);

        $payment = $bankReceipt->payment;

        if (! $payment || ! $payment->isReceiptPayment()) {
            return back()->with('error', 'این پرداخت از نوع رسید بانکی نیست.');
        }

        if (! $payment->isUnderReceiptReview()) {
            return back()->with('error', 'این پرداخت در وضعیت قابل بررسی نیست.');
        }

        if (! $payment->latestBankReceipt || $payment->latestBankReceipt->id !== $bankReceipt->id) {
            return back()->with('error', 'فقط آخرین رسید ثبت‌شده قابل بررسی است.');
        }

        if ($bankReceipt->status !== 'pending_review') {
            return back()->with('error', 'این رسید قبلاً بررسی شده است.');
        }

        app(PaymentStatusService::class)->markFailed(
            $payment,
            'rejected',
            array_merge($payment->callback_data ?? [], [
                'rejection_reason' => $data['rejection_reason'],
                'review_source' => 'admin.bank-receipts.reject',
            ]),
            auth()->id()
        );

        $bankReceipt->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return redirect()
            ->route('admin.bank-receipts.show', $bankReceipt)
            ->with('success', 'رسید بانکی رد شد.');
    }
}