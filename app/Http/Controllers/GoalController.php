<?php 

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $goals = Goal::where('user_id', $user->id)->get();
        
        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'nullable|date|after:today',
            'icon' => 'nullable|string|max:255',
        ]);
        
        $user = auth()->user();
        
        Goal::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'current_amount' => $request->current_amount,
            'target_date' => $request->target_date,
            'icon' => $request->icon,
        ]);
        
        return redirect()->route('goals.index')->with('success', 'Goal created successfully!');
    }

    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);
        
        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'nullable|date|after:today',
            'icon' => 'nullable|string|max:255',
        ]);
        
        $goal->update([
            'name' => $request->name,
            'target_amount' => $request->target_amount,
            'current_amount' => $request->current_amount,
            'target_date' => $request->target_date,
            'icon' => $request->icon,
        ]);
        
        return redirect()->route('goals.index')->with('success', 'Goal updated successfully!');
    }

    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);
        
        $goal->delete();
        
        return redirect()->route('goals.index')->with('success', 'Goal deleted successfully!');
    }
    
    public function contribute(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);
        
        $goal->current_amount += $request->amount;
        
        if ($goal->current_amount > $goal->target_amount) {
            $goal->current_amount = $goal->target_amount;
        }
        
        $goal->save();
        
        return redirect()->route('goals.index')->with('success', 'Contribution added successfully!');
    }
}
