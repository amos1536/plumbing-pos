<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'sale_id',
        'method',
        'amount',
        'transaction_reference',
        'status',
        'paid_at',
        'mpesa_checkout_request_id',
        'mpesa_merchant_request_id',
        'mpesa_phone_number',
        'mpesa_result_code',
        'mpesa_result_description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
