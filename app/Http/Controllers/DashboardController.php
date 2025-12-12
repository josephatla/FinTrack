<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Account;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        // RESTORE DRAFT: If we have a draft from the transaction controller, flash it to 'old' inputs
        if (session()->has('transaction_draft')) {
            session()->flash('_old_input', session('transaction_draft'));
            // Optional: Keep it until successfully submitted? If so, don't forget() here.
            // If you want it one-time only: session()->forget('transaction_draft'); 
        }

        $user = Auth::user();

        $totalIncome = Income::where('user_id', $user->id)->sum('amount');
        $totalExpense = Expense::where('user_id', $user->id)->sum('amount');

        $latestIncomes = Income::with(['category', 'account'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function($i) { $i->type = 'income'; return $i; });

        $latestExpenses = Expense::with(['category', 'account', 'budget'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function($e) { $e->type = 'expense'; return $e; });

        $transactions = $latestIncomes->concat($latestExpenses)
            ->sortByDesc('transaction_date')
            ->take(10);

        $budgets = collect(); 
        if ($user->isPremium()) {
            $budgets = Budget::where('user_id', $user->id)
                ->with('expenses')
                ->get();
        }

        $accounts = Account::where('user_id', $user->id)->get();

        $incomeCategories = Category::where('type', 'income')
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereNull('user_id');
            })
            ->get();

        $expenseCategories = Category::where('type', 'expense')
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereNull('user_id');
            })
            ->get();

        return view('dashboard', compact(
            'totalIncome', 'totalExpense', 'transactions', 
            'budgets', 'incomeCategories', 'expenseCategories', 'accounts'
        ));
    }
}