<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Auth::user()->budgets()->with('expenses')->get();
        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        if (! Auth::user()->isPremium()) {
            return redirect()->route('dashboard')
                ->with('error', __('messages.premium_required_budgets'));
        }

        return view('budgets.create');
    }

    public function store(Request $request)
    {
        if (! Auth::user()->isPremium()) {
            abort(403, __('messages.premium_required_budgets'));
        }

        $request->validate([
            'name'   => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        Budget::create([
            'user_id' => Auth::id(),
            'name'    => $request->name,
            'amount'  => $request->amount,
        ]);

        // UPDATED: Redirect to 'dashboard' so the Transaction Form can restore the draft
        return redirect()->route('dashboard')->with('success', __('budget.created_success'));
    }

    public function show($budget_id)
    {
        $budget = Budget::where('user_id', Auth::id())
                        ->with('expenses')
                        ->findOrFail($budget_id);

        return view('budgets.show', compact('budget'));
    }

    public function destroy(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->delete();
        return redirect()->route('budgets.index')->with('success', __('budget.deleted_success'));
    }
}