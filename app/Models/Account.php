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
        // 'balance' IS REMOVED: Balance must be calculated from transactions, not stored or mass-assigned.
    ];
    
    // Add 'calculated_balance' to $appends so it's included automatically when 
    // the model is cast to JSON/Array (optional, but useful for APIs)
    protected $appends = [
        'calculated_balance'
    ];

    /**
     * Relationship: An account belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: An account has many income transactions.
     */
    public function incomes(): HasMany
    {
        // Using 'account_id' as the foreign key
        return $this->hasMany(Income::class, 'account_id');
    }

    /**
     * Relationship: An account has many expense transactions.
     */
    public function expenses(): HasMany
    {
        // Using 'account_id' as the foreign key
        return $this->hasMany(Expense::class, 'account_id');
    }

    /**
     * Accessor: Calculates the current balance of the account (Income - Expense).
     * * NOTE: This requires 'incomes' and 'expenses' to be eager loaded 
     * in the controller (e.g., Account::with(['incomes', 'expenses'])->get())
     * to prevent N+1 queries.
     */
    public function getCalculatedBalanceAttribute(): float
    {
        // Calculate the balance based on the loaded transactions
        $totalIncome = $this->incomes->sum('amount');
        $totalExpense = $this->expenses->sum('amount');
        
        return $totalIncome - $totalExpense;
    }
}