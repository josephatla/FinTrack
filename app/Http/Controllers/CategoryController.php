<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ADDED: Import Auth facade

class CategoryController extends Controller
{
    public function index()
    {
        // CHANGED: Redirect to dashboard as index view is not needed
        return redirect()->route('dashboard');
    }

    public function create()
    {
        // ADDED: Permission check for accessing the category creation form
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
        
        // ADDED: Permission check for processing the category creation form submission
        if (!$user->isPremium()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'The ability to add new categories is a Premium feature.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ]);

        // CHANGED: Explicitly set user_id to tie the new category to the Premium user
        Category::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
        ]);

        // CHANGED: Redirect to dashboard with success message
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

        // CHANGED: Redirect to dashboard with success message
        return redirect()->route('dashboard')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        // CHANGED: Redirect to dashboard with success message
        return redirect()->route('dashboard')->with('success', 'Category deleted successfully!');
    }
}