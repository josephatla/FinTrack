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
            'budget_name'   => 'required|string|max:255',
            'budget_amount' => 'required|numeric|min:0',
        ]);

        Budget::create([
            'user_id' => Auth::id(),
            'name'    => $request->budget_name,  
            'amount'  => $request->budget_amount, 
        ]);

        session()->forget('budget_draft');

        return redirect()->route('dashboard')->with('success', __('budget.created_success'));
    }

    public function show($budget_id)
    {
        $budget = Budget::where('user_id', Auth::id())
                        ->with('expenses')
                        ->findOrFail($budget_id);
        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }
        return view('budgets.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'budget_name'   => 'required|string|max:255',
            'budget_amount' => 'required|numeric|min:0',
        ]);

        $budget->update([
            'name'   => $request->budget_name,
            'amount' => $request->budget_amount,
        ]);

        return redirect()->route('dashboard')->with('success', __('budget.updated_success'));
    }

    public function destroy(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }
        $budget->delete();
        return redirect()->route('dashboard')->with('success', __('budget.deleted_success'));
    }
}