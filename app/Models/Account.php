<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $primaryKey = 'account_id';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        ];
    
    protected $appends = [
        'calculated_balance'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'account_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'account_id');
    }

    public function getCalculatedBalanceAttribute(): float
    {
        $totalIncome = $this->incomes->sum('amount');
        $totalExpense = $this->expenses->sum('amount');
        
        return $totalIncome - $totalExpense;
    }
}