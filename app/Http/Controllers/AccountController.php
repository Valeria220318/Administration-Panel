<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)->get();
        
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:checking,savings,credit,investment,cash',
            'balance' => 'required|numeric',
        ]);
        
        $user = auth()->user();
        
        Account::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
            'balance' => $request->balance,
        ]);
        
        return redirect()->route('accounts.index')->with('success', 'Account created successfully!');
    }

    public function show(Account $account)
    {
        $this->authorize('view', $account);
        
        $transactions = $account->transactions()
            ->orderBy('date', 'desc')
            ->paginate(15);
            
        return view('accounts.show', compact('account', 'transactions'));
    }

    public function edit(Account $account)
    {
        $this->authorize('update', $account);
        
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorize('update', $account);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:checking,savings,credit,investment,cash',
        ]);
        
        $account->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);
        
        return redirect()->route('accounts.index')->with('success', 'Account updated successfully!');
    }

    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);
        
        // Check if account has transactions
        if ($account->transactions()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete account with transactions. Please delete all transactions first.');
        }
        
        $account->delete();
        
        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully!');
    }
}
