<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'amount',
        'payment_status',
        'bank_transaction_id',
        'bank_reference_id',
        'card_number',
        'description',
        'status',
        'user_id',
        'invoice_id',
        'user_bank_id',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
