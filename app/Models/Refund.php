<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = ['payment_id', 'amount', 'status', 'reason', 'processed_by', 'processed_at', 'gateway_refund_id'];

    protected $casts = ['processed_at' => 'datetime'];

    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class, 'processed_by'); }
}
