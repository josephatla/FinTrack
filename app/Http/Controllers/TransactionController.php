<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Account; // <-- MUST BE IMPORTED
use App\Models\User;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $month = $request->get('month');
        $year = $request->get('year');
        $search = $request->get('search');
        $category = $request->get('category');
        $type = $request->get('type');
        $account = $request->get('account'); // <-- NEW: Retrieve account filter

        // --- FETCH DATA FOR DROPDOWNS ---
        
        // Fetch all user accounts (for the new filter dropdown)
        $accounts = Account::where('user_id', $userId)->get(); 

        // Fetch all categories (user-specific and default) for the dropdown menu
        $categories = Category::where('user_id', $userId)
            ->orWhereNull('user_id')
            ->get();
        
        // --- BASE QUERIES (Using DB::table for UNION and JOIN) ---
        
        // 1. INCOME QUERY
        $incomeQuery = DB::table('incomes')
            ->select(
                'incomes.income_id as id', 'incomes.name', 'incomes.amount', 
                'incomes.transaction_date', 'incomes.category_id', 
                'incomes.account_id', // <-- MUST SELECT account_id
                DB::raw("NULL as budget_name"),
                DB::raw("'income' as type")
            )
            ->where('incomes.user_id', $userId);
        
        // 2. EXPENSE QUERY (Uses LEFT JOIN for budget name)
        $expenseQuery = DB::table('expenses')
            ->select(
                'expenses.expense_id as id', 'expenses.name', 'expenses.amount', 
                'expenses.transaction_date', 'expenses.category_id',
                'expenses.account_id', // <-- MUST SELECT account_id
                'budgets.name as budget_name', 
                DB::raw("'expense' as type")
            )
            ->where('expenses.user_id', $userId)
            ->leftJoin('budgets', 'expenses.budget_id', '=', 'budgets.budget_id');
        
        // --- APPLY FILTERS ---
        
        // Date Filters
        if ($year) {
            $incomeQuery->whereYear('incomes.transaction_date', $year);
            $expenseQuery->whereYear('expenses.transaction_date', $year);
        }
        
        if ($month) {
            $incomeQuery->whereMonth('incomes.transaction_date', $month);
            $expenseQuery->whereMonth('expenses.transaction_date', $month);
        }

        // Search Filter (on name)
        if ($search) {
            $searchTerm = '%' . $search . '%';
            $incomeQuery->where('incomes.name', 'LIKE', $searchTerm);
            $expenseQuery->where('expenses.name', 'LIKE', $searchTerm);
        }

        // Category Filter (on category_id)
        if ($category) {
            $incomeQuery->where('incomes.category_id', $category);
            $expenseQuery->where('expenses.category_id', $category);
        }
        
        // NEW: Account Filter (Filter by selected wallet)
        if ($account) {
            $incomeQuery->where('incomes.account_id', $account);
            $expenseQuery->where('expenses.account_id', $account);
        }


        // --- TYPE FILTER LOGIC ---
        
        if ($type === 'income') {
            $finalQuery = $incomeQuery;
        } elseif ($type === 'expense') {
            $finalQuery = $expenseQuery;
        } else {
            // Default: Run the full union
            $finalQuery = $incomeQuery->unionAll($expenseQuery);
        }

        // --- UNION AND PAGINATION ---

        // Get the combined results from the sub-query and apply ordering/pagination
        $transactions = DB::table(DB::raw("({$finalQuery->toSql()}) as t"))
            ->mergeBindings($finalQuery) // Correct way to merge bindings
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        return view('transactions.index', compact(
            'transactions', 
            'categories',
            'accounts' // <-- Passed accounts to the view
        ));
    }

    public function store(Request $request)
    {
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

        // 2. Budget Gating for Expenses
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

        // 4. Localization of Success Message
        return redirect()->back()->with('success', __('messages.transaction_added_success'));
    }
}