<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    // The index route should be protected by middleware, but eager loading here is good practice.
    public function index()
    {
        // Eager load expenses to prevent N+1 query issue if iterating over budgets in the index view
        $budgets = Auth::user()->budgets()->with('expenses')->get();
        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        // Gating Check 1: Redirect before loading the creation form (Better UX)
        if (! Auth::user()->isPremium()) {
            return redirect()->route('dashboard')
                ->with('error', __('messages.premium_required_budgets'));
        }

        return view('budgets.create');
    }

    public function store(Request $request)
    {
        // Gating Check 2: Hard abort on illegal submission (Security)
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

        // Localization: Success message for creation
        return redirect()->back()->with('success', __('budget.created_success'));
    }

    public function show($budget_id)
    {
        // Access Control & Eager Loading: Ensure ownership and load transactions
        $budget = Budget::where('user_id', Auth::id())
                        ->with('expenses')
                        ->findOrFail($budget_id);

        return view('budgets.show', compact('budget'));
    }
    
    // Note: If you add an update method (edit/update), it would follow the same pattern:
    /*
    public function update(Request $request, Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }
        // ... validation and update logic ...
        return redirect()->route('budgets.show', $budget)->with('success', __('budget.updated_success'));
    }
    */

    public function destroy(Budget $budget)
    {
        // Access Control: Ensure the user owns the budget they are trying to delete
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->delete();
        // Localization: Success message for deletion
        return redirect()->route('budgets.index')->with('success', __('budget.deleted_success'));
    }
}