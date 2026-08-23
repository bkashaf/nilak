<?php

namespace App\Console\Commands;

use App\Domain\Payment\Services\PaymentStatusService;
use App\Models\Payment;
use Illuminate\Console\Command;

class ExpirePayments extends Command
{
    protected $signature = 'payments:expire';

    protected $description = 'Expire initiated payments past their deadline';

    public function handle(PaymentStatusService $statusService): int
    {
        $payments = Payment::query()
            ->where('status', 'initiated')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($payments as $payment) {
            $statusService->markFailed($payment, 'expired', null, null);
        }

        $this->info("Expired {$payments->count()} payment(s).");

        return self::SUCCESS;
    }
}
