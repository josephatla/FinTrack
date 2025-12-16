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
            ->withSum('incomes as total_income', 'amount')
            ->withSum('expenses as total_expense', 'amount')
            ->get()
            ->map(function ($account) {
                $account->calculated_balance = ($account->total_income ?? 0) - ($account->total_expense ?? 0);
                return $account;
            });
            
        return view('accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $accountCount = Account::where('user_id', $user->id)->count();
        if (!$user->isPremium() && $accountCount >= 5) {
            return redirect()->back()->with('error', __('messages.account_limit_reached'));
        }

        $request->validate([
            'name'   => 'required|string|max:255',
            'type'   => 'required|string|max:50', 
        ]);

        $data = $request->except('balance'); 
        $data['user_id'] = $user->id;

        Account::create($data);

        return redirect()->route('accounts.index')->with('success', __('messages.account_created_success'));
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

        return redirect()->route('accounts.index')->with('success', __('messages.account_updated_success'));
    }

    public function destroy(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }
        
        $account->delete();

        return redirect()->route('accounts.index')->with('success', __('messages.account_deleted_success'));
    }
}