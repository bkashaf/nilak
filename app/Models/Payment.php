<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, Order::class, 'id', 'id', 'order_id', 'user_id');
    }

    public function method()
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
}