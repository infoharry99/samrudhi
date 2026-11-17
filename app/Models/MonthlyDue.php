<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlyDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_register_id',
        'member_id',
        'loan_id',
        'monthly_deposit',
        'loan_amount',
        'interest',
        'monthly_installment',
        'penalty',
        'total_payable',
        'remaining_loan_balance',
        'amount_paid',
        'deposit_paid',
        'interest_paid',
        'installment_paid',
        'penalty_paid',
        'payment_status',
        'payment_date',
        'payment_mode',
        'payment_reference',
        'remarks',
    ];

    protected $casts = [
        'monthly_deposit' => 'decimal:2',
        'loan_amount' => 'decimal:2',
        'interest' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'penalty' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'remaining_loan_balance' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'deposit_paid' => 'decimal:2',
        'interest_paid' => 'decimal:2',
        'installment_paid' => 'decimal:2',
        'penalty_paid' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function monthlyRegister(): BelongsTo
    {
        return $this->belongsTo(MonthlyRegister::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function calculateTotalPayable(): void
    {
        $this->total_payable = round(
            $this->monthly_deposit +
            $this->interest +
            $this->monthly_installment +
            $this->penalty,
            2
        );
    }
}
