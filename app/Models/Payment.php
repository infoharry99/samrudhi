<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'member_id',
        'monthly_due_id',
        'loan_id',
        'payment_date',
        'payment_month',
        'total_received',
        'deposit_portion',
        'interest_portion',
        'installment_portion',
        'penalty_portion',
        'payment_mode',
        'payment_status',
        'transaction_reference',
        'gateway_order_id',
        'gateway_payment_id',
        'gateway_signature',
        'recorded_by',
        'remarks',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'total_received' => 'decimal:2',
        'deposit_portion' => 'decimal:2',
        'interest_portion' => 'decimal:2',
        'installment_portion' => 'decimal:2',
        'penalty_portion' => 'decimal:2',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function monthlyDue(): BelongsTo
    {
        return $this->belongsTo(MonthlyDue::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function generateReceiptNumber(): string
    {
        $prefix = 'SSS-' . date('Ym') . '-';
        $latest = self::where('receipt_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $num = (int) substr($latest->receipt_number, -4);
            $next = str_pad($num + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $prefix . $next;
    }
}
