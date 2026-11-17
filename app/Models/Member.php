<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_code',
        'user_id',
        'full_name',
        'mobile_number',
        'alternate_mobile',
        'address',
        'joining_date',
        'monthly_deposit_amount',
        'initial_deposit_balance',
        'initial_loan_balance',
        'current_deposit_balance',
        'current_loan_balance',
        'photo_path',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'monthly_deposit_amount' => 'decimal:2',
        'initial_deposit_balance' => 'decimal:2',
        'initial_loan_balance' => 'decimal:2',
        'current_deposit_balance' => 'decimal:2',
        'current_loan_balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class)->orderBy('id', 'desc');
    }

    public function activeLoan(): HasOne
    {
        return $this->hasOne(Loan::class)->where('status', 'active');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(MemberDeposit::class)->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');
    }

    public function monthlyDues(): HasMany
    {
        return $this->hasMany(MonthlyDue::class)->orderBy('id', 'desc');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date', 'desc')->orderBy('id', 'desc');
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class)->orderBy('penalty_date', 'desc');
    }

    public function loanTransactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class)->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Recalculate member's total savings deposit and current outstanding loan balance
     */
    public function recalculateBalances(): void
    {
        // Savings = Initial Balance + Sum of monthly deposits & interest credits - withdrawals
        $depositsSum = $this->deposits()->sum('amount');
        $this->current_deposit_balance = $this->initial_deposit_balance + $depositsSum;

        // Loans = Sum of remaining principal of all active loans
        $activeLoansPrincipal = $this->loans()->whereIn('status', ['active', 'approved'])->sum('remaining_principal');
        $this->current_loan_balance = $activeLoansPrincipal;

        $this->save();
    }
}
