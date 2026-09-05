<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Domain\Payment\Services\RefundService;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use InvalidArgumentException;
use RuntimeException;

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

        try {
            app(PaymentStatusService::class)->applyManualStatus(
                $payment,
                $data['status'],
                auth()->id(),
                in_array($data['status'], ['failed', 'rejected', 'expired'], true)
                    ? array_merge($payment->callback_data ?? [], [
                        'review_source' => 'admin.payments.update',
                    ])
                    : null
            );
        } catch (RuntimeException | InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
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