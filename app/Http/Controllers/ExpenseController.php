<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Account;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('user_id', Auth::id())->latest()->get();
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $accounts = Account::where('user_id', $user->id)->get();
        $categories = Category::where('user_id', $user->id)->get();
        
        $budgets = collect(); 
        if ($user->isPremium()) {
            $budgets = Budget::where('user_id', $user->id)->get();
        }
        
        return view('expenses.create', compact('accounts', 'categories', 'budgets'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'account_id' => 'required|exists:accounts,account_id',
            'category_id' => 'required|exists:categories,category_id',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $budgetId = $request->budget_id;
        
        if ($user->isPremium()) {
            $request->validate(['budget_id' => 'nullable|exists:budgets,budget_id']);
            
        } else {
            $budgetId = null;
        }

        Expense::create([
            'user_id' => $user->id,
            'account_id' => $request->account_id,
            'budget_id' => $budgetId, 
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }
}