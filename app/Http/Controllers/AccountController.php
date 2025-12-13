<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Income; 
use App\Models\Expense;

class AccountController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $accounts = Account::where('user_id', $userId)
            ->with(['incomes', 'expenses'])
            ->get();
            
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'type'   => 'required|string|max:50', 
        ]);

        $data = $request->except('balance'); 
        $data['user_id'] = Auth::id();

        Account::create($data);
        return redirect()->route('accounts.index')->with('success', __('dashboard.account_created_success'));
    }

    public function show(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }
        return redirect()->route('accounts.transactions', $account->account_id);
    }
    
    public function showTransactions(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        return redirect()->route('transactions.index', [
            'account' => $account->account_id,
            'account_name' => $account->name 
        ]);
    }

    public function edit(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'name'   => 'required|string|max:255',
            'type'   => 'required|string|max:50',
        ]);

        $data = $request->except('balance');
        $account->update($data);

        return redirect()->route('accounts.index')->with('success', __('dashboard.account_updated_success'));
    }

    public function destroy(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }
        
        $account->delete();

        return redirect()->route('accounts.index')->with('success', __('dashboard.account_deleted_success'));
    }
}