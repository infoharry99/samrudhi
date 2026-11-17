<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'monthly_due_id',
        'penalty_date',
        'amount',
        'reason',
        'status',
        'waived_by',
        'waiver_reason',
    ];

    protected $casts = [
        'penalty_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function monthlyDue(): BelongsTo
    {
        return $this->belongsTo(MonthlyDue::class);
    }

    public function waiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waived_by');
    }
}
