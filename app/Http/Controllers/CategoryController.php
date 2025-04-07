<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $incomeCategories = Category::where('user_id', $user->id)
            ->where('type', 'income')
            ->get();
            
        $expenseCategories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->get();
            
        return view('categories.index', compact('incomeCategories', 'expenseCategories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:income,expense',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
        ]);
        
        $user = auth()->user();
        
        Category::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color ?? '#' . dechex(rand(0x000000, 0xFFFFFF)),
            'icon' => $request->icon,
        ]);
        
        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
        ]);
        
        $category->update([
            'name' => $request->name,
            'color' => $request->color,
            'icon' => $request->icon,
        ]);
        
        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        
        // Check if category has transactions
        if ($category->transactions()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with transactions. Please reassign transactions first.');
        }
        
        // Check if category has budget
        if ($category->budget) {
            $category->budget->delete();
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }
}
