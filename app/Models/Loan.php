<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_number',
        'member_id',
        'application_date',
        'approval_date',
        'loan_amount',
        'interest_rate',
        'interest_calculation_method',
        'monthly_installment',
        'loan_duration_months',
        'first_installment_date',
        'loan_purpose',
        'remarks',
        'status',
        'remaining_principal',
        'total_interest_charged',
        'total_interest_paid',
        'total_principal_paid',
    ];

    protected $casts = [
        'application_date' => 'date',
        'approval_date' => 'date',
        'first_installment_date' => 'date',
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'remaining_principal' => 'decimal:2',
        'total_interest_charged' => 'decimal:2',
        'total_interest_paid' => 'decimal:2',
        'total_principal_paid' => 'decimal:2',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class)->orderBy('transaction_date', 'asc')->orderBy('id', 'asc');
    }

    public function monthlyDues(): HasMany
    {
        return $this->hasMany(MonthlyDue::class)->orderBy('id', 'desc');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date', 'desc');
    }

    /**
     * Calculate monthly interest according to method:
     * - Reducing Balance: (Remaining Principal * monthly rate%)
     * - Flat: (Initial Principal * monthly rate%)
     * - Fixed Monthly: configured fixed amount or fixed percentage
     */
    public function calculateMonthlyInterest(): float
    {
        if ($this->remaining_principal <= 0) {
            return 0.0;
        }

        $ratePercent = (float) $this->interest_rate; // e.g. 1.0 means 1% per month

        switch ($this->interest_calculation_method) {
            case 'flat':
                return round(($this->loan_amount * $ratePercent) / 100, 2);
            case 'fixed_monthly':
                return round(($this->loan_amount * $ratePercent) / 100, 2);
            case 'reducing_balance':
            default:
                return round(($this->remaining_principal * $ratePercent) / 100, 2);
        }
    }
}
