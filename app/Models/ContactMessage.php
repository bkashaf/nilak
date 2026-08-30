<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    protected $table = 'contact_messages';

    protected $fillable = [
        'user_id',   // برای فرم ادمین
        'subject',   // برای فرم ادمین
        'name',      // برای فرم فرانت
        'email',     // برای فرم فرانت
        'message',   // مشترک
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
