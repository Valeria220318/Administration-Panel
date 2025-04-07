<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $budgets = Budget::where('user_id', $user->id)
            ->with('category')
            ->get();
            
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereDoesntHave('budget')
            ->get();
            
        return view('budgets.index', compact('budgets', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:monthly,yearly',
        ]);
        
        $user = auth()->user();
        $category = Category::findOrFail($request->category_id);
        
        if ($category->user_id !== $user->id) {
            return redirect()->back()->with('error', 'You do not have permission to create a budget for this category.');
        }
        
        // Check if budget already exists for this category
        $existingBudget = Budget::where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->first();
            
        if ($existingBudget) {
            return redirect()->back()->with('error', 'A budget already exists for this category.');
        }
        
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'period' => $request->period,
        ]);
        
        return redirect()->route('budgets.index')->with('success', 'Budget created successfully!');
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:monthly,yearly',
        ]);
        
        $budget->update([
            'amount' => $request->amount,
            'period' => $request->period,
        ]);
        
        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully!');
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        
        $budget->delete();
        
        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully!');
    }
}

