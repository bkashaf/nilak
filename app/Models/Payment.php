<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'idempotency_key',
        'payment_method_id',
        'amount',
        'status',
        'gateway_name',
        'gateway_transaction_id',
        'callback_data',
        'paid_at',
        'expires_at',
    ];

    protected $casts = [
        'callback_data' => 'array',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Order::class, 'id', 'id', 'order_id', 'user_id');
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(PaymentStatusHistory::class)->latest();
    }

    public function bankReceipts(): HasMany
    {
        return $this->hasMany(BankReceipt::class)->latest('id');
    }

    public function latestBankReceipt(): HasOne
    {
        return $this->hasOne(BankReceipt::class)->latestOfMany();
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function isReceiptPayment(): bool
    {
        $method = $this->resolvedMethod();

        if (! $method) {
            return false;
        }

        return $method->type === 'receipt' || $method->name === 'bank_receipt';
    }

    public function canUploadReceipt(): bool
    {
        return $this->isReceiptPayment()
            && in_array($this->status, ['pending', 'initiated', 'rejected'], true);
    }

    public function isAwaitingReceipt(): bool
    {
        return $this->isReceiptPayment()
            && in_array($this->status, ['pending', 'initiated'], true)
            && ! $this->hasUploadedReceipt();
    }

    public function isUnderReceiptReview(): bool
    {
        return $this->status === 'pending_review';
    }

    public function hasUploadedReceipt(): bool
    {
        return $this->resolvedLatestBankReceipt() !== null;
    }

    private function resolvedMethod(): ?PaymentMethod
    {
        if ($this->relationLoaded('method')) {
            return $this->method;
        }

        return $this->method()->first();
    }

    private function resolvedLatestBankReceipt(): ?BankReceipt
    {
        if ($this->relationLoaded('latestBankReceipt')) {
            return $this->latestBankReceipt;
        }

        if ($this->relationLoaded('bankReceipts')) {
            return $this->bankReceipts->sortByDesc('id')->first();
        }

        return $this->latestBankReceipt()->first();
    }
}