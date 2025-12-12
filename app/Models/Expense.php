<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';
    protected $primaryKey = 'expense_id';

    protected $fillable = [
        'user_id',
        'account_id',
        'budget_id',
        'category_id',
        'name',
        'amount',
        'transaction_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
