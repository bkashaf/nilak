<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function statusHistories()
    {
        return $this->hasMany(PaymentStatusHistory::class)->latest();
    }

    public function bankReceipts()
    {
        return $this->hasMany(BankReceipt::class);
    }
}
