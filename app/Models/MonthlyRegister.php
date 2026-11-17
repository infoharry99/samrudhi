<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlyRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'title',
        'generated_date',
        'status',
        'total_deposit_expected',
        'total_interest_expected',
        'total_installment_expected',
        'total_penalty_expected',
        'total_payable_expected',
        'total_collected',
        'notes',
    ];

    protected $casts = [
        'generated_date' => 'date',
        'total_deposit_expected' => 'decimal:2',
        'total_interest_expected' => 'decimal:2',
        'total_installment_expected' => 'decimal:2',
        'total_penalty_expected' => 'decimal:2',
        'total_payable_expected' => 'decimal:2',
        'total_collected' => 'decimal:2',
    ];

    public function dues(): HasMany
    {
        return $this->hasMany(MonthlyDue::class)->orderBy('id', 'asc');
    }

    public function recalculateTotals(): void
    {
        $this->total_deposit_expected = $this->dues()->sum('monthly_deposit');
        $this->total_interest_expected = $this->dues()->sum('interest');
        $this->total_installment_expected = $this->dues()->sum('monthly_installment');
        $this->total_penalty_expected = $this->dues()->sum('penalty');
        $this->total_payable_expected = $this->dues()->sum('total_payable');
        $this->total_collected = $this->dues()->sum('amount_paid');
        $this->save();
    }
}
