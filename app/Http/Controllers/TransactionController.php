<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Transaction::where('user_id', $user->id)->with(['account', 'category']);
        
        // Apply filters
        if ($request->has('start_date') && $request->start_date) {
            $query->where('date', '>=', Carbon::parse($request->start_date));
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->where('date', '<=', Carbon::parse($request->end_date));
        }
        
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('account_id') && $request->account_id) {
            $query->where('account_id', $request->account_id);
        }
        
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        $transactions = $query->orderBy('date', 'desc')->paginate(20);
        $accounts = $user->accounts;
        $categories = $user->categories;
        
        return view('transactions.index', compact('transactions', 'accounts', 'categories'));
    }

    public function create()
    {
        $user = auth()->user();
        $accounts = $user->accounts;
        $categories = $user->categories;
        
        return view('transactions.create', compact('accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense,transfer',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|required_if:is_recurring,1|string',
        ]);
        
        $user = auth()->user();
        $account = Account::findOrFail($request->account_id);
        
        if ($account->user_id !== $user->id) {
            return redirect()->back()->with('error', 'You do not have permission to add transactions to this account.');
        }
        
        // Update account balance
        if ($request->type === 'income') {
            $account->balance += $request->amount;
        } elseif ($request->type === 'expense') {
            $account->balance -= $request->amount;
        } elseif ($request->type === 'transfer' && $request->has('transfer_to_account_id')) {
            $toAccount = Account::findOrFail($request->transfer_to_account_id);
            if ($toAccount->user_id !== $user->id) {
                return redirect()->back()->with('error', 'You do not have permission to transfer to this account.');
            }
            
            $account->balance -= $request->amount;
            $toAccount->balance += $request->amount;
            $toAccount->save();
            
            // Create the receiving transaction
            Transaction::create([
                'user_id' => $user->id,
                'account_id' => $toAccount->id,
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'type' => 'income',
                'description' => 'Transfer from ' . $account->name,
                'date' => $request->date,
                'is_recurring' => false,
            ]);
        }
        
        $account->save();
        
        $transaction = new Transaction([
            'user_id' => $user->id,
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'description' => $request->description,
            'date' => $request->date,
            'is_recurring' => $request->is_recurring ?? false,
            'recurring_frequency' => $request->recurring_frequency,
        ]);
        
        $transaction->save();
        
        return redirect()->route('transactions.index')->with('success', 'Transaction added successfully!');
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $user = auth()->user();
        $accounts = $user->accounts;
        $categories = $user->categories;
        
        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense,transfer',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|required_if:is_recurring,1|string',
        ]);
        
        $user = auth()->user();
        $account = Account::findOrFail($request->account_id);
        
        if ($account->user_id !== $user->id) {
            return redirect()->back()->with('error', 'You do not have permission to update this transaction.');
        }
        
        // Revert the old transaction's effect on the account balance
        if ($transaction->type === 'income') {
            $transaction->account->balance -= $transaction->amount;
        } elseif ($transaction->type === 'expense') {
            $transaction->account->balance += $transaction->amount;
        }
        $transaction->account->save();
        
        // Apply the new transaction's effect
        if ($request->type === 'income') {
            $account->balance += $request->amount;
        } elseif ($request->type === 'expense') {
            $account->balance -= $request->amount;
        }
        $account->save();
        
        $transaction->update([
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'description' => $request->description,
            'date' => $request->date,
            'is_recurring' => $request->is_recurring ?? false,
            'recurring_frequency' => $request->recurring_frequency,
        ]);
        
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully!');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        
        // Revert the transaction's effect on the account balance
        if ($transaction->type === 'income') {
            $transaction->account->balance -= $transaction->amount;
        } elseif ($transaction->type === 'expense') {
            $transaction->account->balance += $transaction->amount;
        }
        $transaction->account->save();
        
        $transaction->delete();
        
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully!');
    }
}

