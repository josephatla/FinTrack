<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class CategoryController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function create()
    {
        if (!Auth::user()->isPremium()) {
             return redirect()
                 ->route('dashboard')
                 ->with('error', 'Only Premium users can create new categories.');
        }

        return view('categories.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isPremium()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'The ability to add new categories is a Premium feature.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ]);

        Category::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
        ]);
        return redirect()->route('dashboard')->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ]);

        $category->update($request->all());
        return redirect()->route('dashboard')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('dashboard')->with('success', 'Category deleted successfully!');
    }
}