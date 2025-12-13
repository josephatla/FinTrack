<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function storeDraft(Request $request)
    {
        session(['transaction_draft' => $request->except(['_token'])]);
        return redirect()->route('categories.create');
    }

    public function storeBudgetDraft(Request $request)
    {
        session(['transaction_draft' => $request->except(['_token'])]);
        return redirect()->route('budgets.create');
    }

    public function autosave(Request $request)
    {
        $data = $request->input('data', []);
        $form = $request->input('form');

        if ($form === 'transaction') {
            session(['transaction_draft' => $data]);
        } elseif ($form === 'budget') {
            session(['budget_draft' => $data]);
        }

        return response()->json(['status' => 'success']);
    }
}