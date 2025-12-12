<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Income; // Import if you need to manually query transactions
use App\Models\Expense; // Import if you need to manually query transactions

class AccountController extends Controller
{
    /**
     * Display a listing of the user's accounts (wallets) with calculated balances.
     * AUTHENTICATED: Only loads accounts belonging to the current user.
     * PERFORMANCE: Eager loads transactions for efficient balance calculation.
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Eager load incomes and expenses to calculate the balance efficiently (N+1 fix)
        // 2. Filter by user_id for security
        $accounts = Account::where('user_id', $userId)
            ->with(['incomes', 'expenses'])
            ->get();
            
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Display the form to create a new account.
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created account in storage.
     * AUTHENTICATED: Associates the account with the current user.
     * LOGIC: Removes 'balance' from input; balances are calculated, not stored directly.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'type'   => 'required|string|max:50', // Added max length
            // 'balance' removed as it is calculated
        ]);

        // Merge user_id and strip 'balance' before creating
        $data = $request->except('balance'); 
        $data['user_id'] = Auth::id();

        Account::create($data);

        return redirect()->route('accounts.index')->with('success', __('dashboard.account_created_success'));
    }

    /**
     * Display the specified account. (Not needed based on your current UI plan)
     */
    public function show(Account $account)
    {
        // AUTHORIZATION: Policy check (or manual check)
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }
        
        // This is usually reserved for a detailed account view, but we are using showTransactions below.
        return redirect()->route('accounts.transactions', $account->account_id);
    }
    
    /**
     * Custom method to view transaction history filtered by a specific account.
     */
    public function showTransactions(Account $account)
    {
        // AUTHORIZATION: Ensure the user owns the account
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        // Redirect to the general transaction history page, passing the account_id as a filter
        return redirect()->route('transactions.index', [
            'account' => $account->account_id,
            // You can pass the account name to display in the header of the history page
            'account_name' => $account->name 
        ]);
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit(Account $account)
    {
        // AUTHORIZATION: Policy check (or manual check)
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified account in storage.
     */
    public function update(Request $request, Account $account)
    {
        // AUTHORIZATION: Ensure the user owns the account
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'name'   => 'required|string|max:255',
            'type'   => 'required|string|max:50',
            // 'balance' removed
        ]);

        $data = $request->except('balance');
        $account->update($data);

        return redirect()->route('accounts.index')->with('success', __('dashboard.account_updated_success'));
    }

    /**
     * Remove the specified account from storage.
     */
    public function destroy(Account $account)
    {
        // AUTHORIZATION: Ensure the user owns the account
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }
        
        $account->delete();

        return redirect()->route('accounts.index')->with('success', __('dashboard.account_deleted_success'));
    }
}