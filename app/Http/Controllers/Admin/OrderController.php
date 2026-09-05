<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $orders = Order::query()
            ->with([
                'user',
                'payments.method',
                'payments.latestBankReceipt',
            ])
            ->latest()
            ->paginate(20);

        return view('themes.admin.orders.index', compact('orders'));
    }

    public function edit(Order $order)
    {
        $order->load([
            'user',
            'items.product',
            'payments.method',
            'payments.latestBankReceipt',
            'payments.bankReceipts.reviewer',
            'payments.bankReceipts.uploader',
        ]);

        $payment = $order->payments->sortByDesc('id')->first();
        $latestReceipt = $payment?->latestBankReceipt;

        return view('themes.admin.orders.edit', compact('order', 'payment', 'latestReceipt'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,paid,canceled,shipped,delivered'],
            'payment_status' => ['required', 'string', 'in:pending,initiated,pending_review,paid,failed,rejected,expired'],
            'tracking_code' => ['nullable', 'string', 'max:100'],
        ]);

        $payment = $order->payments()->with([
            'method',
            'latestBankReceipt',
            'bankReceipts',
        ])->latest('id')->first();

        $effectivePaymentStatus = $payment
            ? $this->resolveRequestedPaymentStatus($payment, $data['status'], $data['payment_status'])
            : null;

        try {
            DB::transaction(function () use ($order, $payment, $data, $effectivePaymentStatus) {
                $oldStatus = $order->status;

                $order->update([
                    'status' => $data['status'],
                    'tracking_code' => $data['tracking_code'],
                ]);

                if ($oldStatus !== $data['status']) {
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'from_status' => $oldStatus,
                        'to_status' => $data['status'],
                        'changed_by' => auth()->id(),
                    ]);
                }

                if (! $payment || ! $effectivePaymentStatus) {
                    return;
                }

                $callbackData = in_array($effectivePaymentStatus, ['failed', 'rejected', 'expired'], true)
                    ? array_merge($payment->callback_data ?? [], [
                        'review_source' => 'admin.orders.update',
                    ])
                    : null;

                app(PaymentStatusService::class)->applyManualStatus(
                    $payment,
                    $effectivePaymentStatus,
                    auth()->id(),
                    $callbackData
                );
            });
        } catch (RuntimeException | InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.orders.edit', $order)
            ->with('success', 'وضعیت سفارش و شماره پیگیری به‌روزرسانی شد.');
    }

    private function resolveRequestedPaymentStatus(Payment $payment, string $orderStatus, string $requestedPaymentStatus): string
    {
        if ($orderStatus === 'delivered' && $payment->method?->type === 'cod') {
            return 'paid';
        }

        return $requestedPaymentStatus;
    }
}