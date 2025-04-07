<?php

namespace App\Http\Controllers;

use App\Models\Widget;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $widgets = Widget::where('user_id', $user->id)
            ->orderBy('position')
            ->get();
            
        $availableWidgetTypes = [
            'total_balance' => 'Total Balance',
            'income' => 'Income',
            'bills' => 'Bills',
            'total_savings' => 'Total Savings',
            'money_flow' => 'Money Flow',
            'budget' => 'Budget',
            'goals' => 'Goals',
            'recent_transactions' => 'Recent Transactions',
            'expense_breakdown' => 'Expense Breakdown',
        ];
        
        return view('widgets.index', compact('widgets', 'availableWidgetTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'settings' => 'nullable|array',
        ]);
        
        $user = auth()->user();
        
        // Get the next position
        $nextPosition = Widget::where('user_id', $user->id)->max('position') + 1;
        
        Widget::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'position' => $nextPosition,
            'active' => true,
            'settings' => $request->settings,
        ]);
        
        return redirect()->route('dashboard')->with('success', 'Widget added successfully!');
    }

    public function update(Request $request, Widget $widget)
    {
        $this->authorize('update', $widget);
        
        $validated = $request->validate([
            'active' => 'boolean',
            'settings' => 'nullable|array',
        ]);
        
        $widget->update([
            'active' => $request->has('active') ? $request->active : $widget->active,
            'settings' => $request->has('settings') ? $request->settings : $widget->settings,
        ]);
        
        return redirect()->route('widgets.index')->with('success', 'Widget updated successfully!');
    }

    public function destroy(Widget $widget)
    {
        $this->authorize('delete', $widget);
        
        $widget->delete();
        
        // Reorder remaining widgets
        $widgets = Widget::where('user_id', auth()->id())
            ->where('position', '>', $widget->position)
            ->get();
            
        foreach ($widgets as $w) {
            $w->position = $w->position - 1;
            $w->save();
        }
        
        return redirect()->route('widgets.index')->with('success', 'Widget removed successfully!');
    }
    
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'widgets' => 'required|array',
            'widgets.*' => 'exists:widgets,id',
        ]);
        
        $user = auth()->user();
        
        foreach ($request->widgets as $position => $widgetId) {
            $widget = Widget::find($widgetId);
            
            if ($widget && $widget->user_id === $user->id) {
                $widget->position = $position + 1;
                $widget->save();
            }
        }
        
        return response()->json(['success' => true]);
    }
}
