<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::where('user_id', Auth::id())->latest()->get();
        return view('incomes.index', compact('incomes'));
    }

    public function create()
    {
        $accounts = Account::where('user_id', Auth::id())->get();
        $categories = Category::where('user_id', Auth::id())->get();
        return view('incomes.create', compact('accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,account_id',
            'category_id' => 'required|exists:categories,category_id',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        Income::create([
            'user_id' => Auth::id(),
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
        ]);

        return redirect()->route('incomes.index')->with('success', 'Income added successfully.');
    }
}
