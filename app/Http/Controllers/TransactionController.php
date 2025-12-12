<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Account;
use App\Models\User;

class TransactionController extends Controller
{
    // ... index method remains the same ...
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $month = $request->get('month');
        $year = $request->get('year');
        $search = $request->get('search');
        $category = $request->get('category');
        $type = $request->get('type');
        $account = $request->get('account'); 

        $accounts = Account::where('user_id', $userId)->get(); 

        $categories = Category::where('user_id', $userId)
            ->orWhereNull('user_id')
            ->get();
        
        $incomeQuery = DB::table('incomes')
            ->select(
                'incomes.income_id as id', 'incomes.name', 'incomes.amount', 
                'incomes.transaction_date', 'incomes.category_id', 
                'incomes.account_id', 
                DB::raw("NULL as budget_name"),
                DB::raw("'income' as type")
            )
            ->where('incomes.user_id', $userId);
        
        $expenseQuery = DB::table('expenses')
            ->select(
                'expenses.expense_id as id', 'expenses.name', 'expenses.amount', 
                'expenses.transaction_date', 'expenses.category_id',
                'expenses.account_id', 
                'budgets.name as budget_name', 
                DB::raw("'expense' as type")
            )
            ->where('expenses.user_id', $userId)
            ->leftJoin('budgets', 'expenses.budget_id', '=', 'budgets.budget_id');
        
        if ($year) {
            $incomeQuery->whereYear('incomes.transaction_date', $year);
            $expenseQuery->whereYear('expenses.transaction_date', $year);
        }
        
        if ($month) {
            $incomeQuery->whereMonth('incomes.transaction_date', $month);
            $expenseQuery->whereMonth('expenses.transaction_date', $month);
        }

        if ($search) {
            $searchTerm = '%' . $search . '%';
            $incomeQuery->where('incomes.name', 'LIKE', $searchTerm);
            $expenseQuery->where('expenses.name', 'LIKE', $searchTerm);
        }

        if ($category) {
            $incomeQuery->where('incomes.category_id', $category);
            $expenseQuery->where('expenses.category_id', $category);
        }
        
        if ($account) {
            $incomeQuery->where('incomes.account_id', $account);
            $expenseQuery->where('expenses.account_id', $account);
        }

        if ($type === 'income') {
            $finalQuery = $incomeQuery;
        } elseif ($type === 'expense') {
            $finalQuery = $expenseQuery;
        } else {
            $finalQuery = $incomeQuery->unionAll($expenseQuery);
        }

        $transactions = DB::table(DB::raw("({$finalQuery->toSql()}) as t"))
            ->mergeBindings($finalQuery) 
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        return view('transactions.index', compact(
            'transactions', 
            'categories',
            'accounts' 
        ));
    }

    public function store(Request $request)
    {
        // NO INTERCEPT NEEDED: SessionController handles the draft logic via 'formaction'

        $user = Auth::user();

        // 1. Validation
        $request->validate([
            'type'              => 'required|in:income,expense',
            'amount'            => 'required|numeric|min:0',
            'transaction_date'  => 'required|date',
            'name'              => 'required|string|max:255',
            'account_id'        => 'required|exists:accounts,account_id',
            'category_id'       => 'nullable|exists:categories,category_id', 
        ]);

        $userId = Auth::id();

        // 2. Budget Gating
        $budgetId = $request->budget_id;
        if ($request->type === 'expense') {
            if ($user->isPremium()) {
                 $request->validate(['budget_id' => 'nullable|exists:budgets,budget_id']);
            } else {
                $budgetId = null;
            }
        } else {
            $budgetId = null;
        }

        // 3. Creation
        if ($request->type === 'income') {
            Income::create([
                'user_id' => $userId,
                'account_id' => $request->account_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'amount' => $request->amount,
                'transaction_date' => $request->transaction_date,
            ]);
        } else {
            Expense::create([
                'user_id' => $userId,
                'account_id' => $request->account_id,
                'category_id' => $request->category_id,
                'budget_id' => $budgetId,
                'name' => $request->name,
                'amount' => $request->amount,
                'transaction_date' => $request->transaction_date,
            ]);
        }

        // 4. CLEANUP: Clear draft on success (in case one existed)
        session()->forget('transaction_draft');

        return redirect()->back()->with('success', __('messages.transaction_added_success'));
    }
}