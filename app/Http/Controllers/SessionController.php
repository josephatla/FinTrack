<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    // Existing method for Categories
    public function storeDraft(Request $request)
    {
        session(['transaction_draft' => $request->except(['_token'])]);
        return redirect()->route('categories.create');
    }

    // NEW: Method for Budgets
    public function storeBudgetDraft(Request $request)
    {
        // Save the transaction inputs to the same session key
        session(['transaction_draft' => $request->except(['_token'])]);
        
        // Redirect to the budget creation page
        return redirect()->route('budgets.create');
    }
}