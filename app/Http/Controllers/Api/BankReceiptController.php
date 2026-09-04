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
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'tracking_number' => 'nullable|string|max:100',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'note' => 'nullable|string|max:1000',
        ]);

        $payment = Payment::whereHas('order', fn ($query) => $query->where('user_id', $request->user()->id))
            ->findOrFail($request->payment_id);

        if ($payment->method?->type !== 'receipt' || ! in_array($payment->status, ['initiated', 'rejected'], true)) {
            return response()->json(['error' => 'این پرداخت در وضعیت قابل آپلود نیست'], 422);
        }

        $path = $request->file('receipt')->store('receipts', 'public');

        $receipt = BankReceipt::create([
            'payment_id' => $payment->id,
            'tracking_number' => $request->input('tracking_number'),
            'note' => $request->input('note'),
            'file_path' => $path,
            'original_name' => $request->file('receipt')->getClientOriginalName(),
            'uploaded_by' => $request->user()->id,
            'uploaded_at' => now(),
            'status' => 'pending_review',
        ]);

        $payment->update([
            'callback_data' => array_merge($payment->callback_data ?? [], [
                'receipt_path' => $path,
                'tracking_number' => $request->input('tracking_number'),
                'uploaded_at' => now()->toDateTimeString(),
            ]),
            'status' => 'pending_review',
        ]);

        return response()->json([
            'message' => 'رسید با موفقیت آپلود شد',
            'receipt' => $receipt,
            'payment' => $payment->fresh(),
        ]);
    }

    public function approve(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'approved' => 'required|boolean',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $payment = Payment::with(['method', 'bankReceipts'])->findOrFail($request->payment_id);

        if ($payment->method?->type !== 'receipt' || $payment->status !== 'pending_review') {
            return response()->json(['error' => 'این رسید در وضعیت قابل بررسی نیست'], 422);
        }

        $receipt = $payment->bankReceipts()->latest('id')->first();

        if ($request->boolean('approved')) {
            app(PaymentStatusService::class)->markPaid($payment, null, null, $request->user()->id);

            $receipt?->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);
        } else {
            
app(PaymentStatusService::class)->markFailed(
    $payment,
    'rejected',
    [
        'rejection_reason' => $request->input('rejection_reason'),
        'review_source' => 'api.bank-receipts.approve',
    ],
    $request->user()->id
);

            $receipt?->update([
                'status' => 'rejected',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'rejection_reason' => $request->input('rejection_reason'),
            ]);
        }

        return response()->json([
            'message' => $request->boolean('approved') ? 'پرداخت تأیید شد' : 'پرداخت رد شد',
            'payment' => $payment->fresh(),
        ]);
    }
}