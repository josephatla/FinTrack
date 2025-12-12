<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    // Redirects to Category creation (preserving transaction draft)
    public function storeDraft(Request $request)
    {
        session(['transaction_draft' => $request->except(['_token'])]);
        return redirect()->route('categories.create');
    }

    // Redirects to Budget creation (preserving transaction draft)
    public function storeBudgetDraft(Request $request)
    {
        session(['transaction_draft' => $request->except(['_token'])]);
        return redirect()->route('budgets.create');
    }

    // NEW: Handles AJAX auto-saving
    public function autosave(Request $request)
    {
        $data = $request->input('data', []);
        $form = $request->input('form');

        // Store data in separate keys to avoid collision
        if ($form === 'transaction') {
            session(['transaction_draft' => $data]);
        } elseif ($form === 'budget') {
            session(['budget_draft' => $data]);
        }

        return response()->json(['status' => 'success']);
    }
}