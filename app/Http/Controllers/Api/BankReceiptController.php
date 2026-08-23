<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Domain\Payment\Services\PaymentStatusService;
use Illuminate\Support\Facades\Storage;

class BankReceiptController extends Controller
{
    /**
     * آپلود رسید بانکی توسط کاربر
     */
    public function upload(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $payment = Payment::whereHas('order', fn ($query) => $query->where('user_id', $request->user()->id))
            ->findOrFail($request->payment_id);

        if ($payment->status !== 'initiated') {
            return response()->json(['error' => 'این پرداخت در وضعیت قابل آپلود نیست'], 422);
        }

        // ذخیره فایل
        $path = $request->file('receipt')->store('receipts', 'public');

        // ذخیره اطلاعات در callback_data
        $payment->update([
            'callback_data' => [
                'receipt_path' => $path,
                'uploaded_at' => now(),
            ],
            'status' => 'pending_review',
        ]);

        return response()->json([
            'message' => 'رسید با موفقیت آپلود شد',
            'payment' => $payment,
        ]);
    }

    /**
     * تأیید یا رد رسید توسط مدیر
     */
    public function approve(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'approved' => 'required|boolean',
        ]);

        $payment = Payment::findOrFail($request->payment_id);

        if ($request->approved) {
            app(PaymentStatusService::class)->markPaid($payment);
        } else {
            app(PaymentStatusService::class)->markFailed($payment, 'rejected');
        }

        return response()->json([
            'message' => $request->approved ? 'پرداخت تأیید شد' : 'پرداخت رد شد',
            'payment' => $payment,
        ]);
    }
}
