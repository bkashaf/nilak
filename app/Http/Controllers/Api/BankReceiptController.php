<?php

namespace App\Http\Controllers\Api;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Http\Controllers\Controller;
use App\Models\BankReceipt;
use App\Models\Payment;
use Illuminate\Http\Request;

class BankReceiptController extends Controller
{
    public function upload(Request $request)
    {
        $data = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'tracking_number' => 'nullable|string|max:100',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'note' => 'nullable|string|max:1000',
        ]);

        $payment = Payment::whereHas('order', fn ($query) => $query->where('user_id', $request->user()->id))
            ->findOrFail($data['payment_id']);

        $payment->loadMissing([
            'order',
            'method',
            'latestBankReceipt',
            'bankReceipts',
        ]);

        if (! $payment->canUploadReceipt()) {
            return response()->json(['error' => 'این پرداخت در وضعیت قابل آپلود نیست'], 422);
        }

        $file = $request->file('receipt');
        $path = $file->store('receipts', 'public');

        $receipt = BankReceipt::create([
            'payment_id' => $payment->id,
            'tracking_number' => $data['tracking_number'] ?? null,
            'note' => $data['note'] ?? null,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'uploaded_by' => $request->user()->id,
            'uploaded_at' => now(),
            'status' => 'pending_review',
        ]);

        $payment->update([
            'status' => 'pending_review',
            'callback_data' => array_merge($payment->callback_data ?? [], [
                'tracking_number' => $data['tracking_number'] ?? null,
                'receipt_path' => $path,
                'receipt_original_name' => $file->getClientOriginalName(),
                'receipt_note' => $data['note'] ?? null,
                'uploaded_at' => now()->toDateTimeString(),
            ]),
        ]);

        return response()->json([
            'message' => 'رسید با موفقیت آپلود شد',
            'receipt' => $receipt,
            'payment' => $payment->fresh([
                'order',
                'method',
                'latestBankReceipt',
            ]),
        ]);
    }

    public function approve(Request $request)
    {
        $data = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'approved' => 'required|boolean',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $payment = Payment::with([
            'order',
            'method',
            'latestBankReceipt',
            'bankReceipts',
        ])->findOrFail($data['payment_id']);

        if (! $payment->isReceiptPayment() || ! $payment->isUnderReceiptReview()) {
            return response()->json(['error' => 'این رسید در وضعیت قابل بررسی نیست'], 422);
        }

        $receipt = $payment->latestBankReceipt
            ?? $payment->bankReceipts->sortByDesc('id')->first();

        if (! $receipt) {
            return response()->json(['error' => 'برای این پرداخت رسیدی جهت بررسی ثبت نشده است'], 422);
        }

        if ($request->boolean('approved')) {
            app(PaymentStatusService::class)->markPaid($payment, null, null, $request->user()->id);

            $receipt->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);
        } else {
            app(PaymentStatusService::class)->markFailed(
                $payment,
                'rejected',
                array_merge($payment->callback_data ?? [], [
                    'rejection_reason' => $data['rejection_reason'] ?? null,
                    'review_source' => 'api.bank-receipts.approve',
                ]),
                $request->user()->id
            );

            $receipt->update([
                'status' => 'rejected',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'rejection_reason' => $data['rejection_reason'] ?? null,
            ]);
        }

        return response()->json([
            'message' => $request->boolean('approved') ? 'پرداخت تأیید شد' : 'پرداخت رد شد',
            'payment' => $payment->fresh([
                'order',
                'method',
                'latestBankReceipt',
            ]),
        ]);
    }
}