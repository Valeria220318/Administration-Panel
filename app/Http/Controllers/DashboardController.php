<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Budget;
use App\Models\Widget;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $widgets = $user->widgets()->where('active', true)->orderBy('position')->get();
        
        $dashboardData = [
            'total_balance' => $user->getTotalBalance(),
            'total_savings' => $user->getSavingsTotal(),
            'monthly_income' => $this->getMonthlyIncome(),
            'monthly_expenses' => $this->getMonthlyExpenses(),
            'budget_usage' => $this->getBudgetUsage(),
            'money_flow' => $this->getMoneyFlow(),
            'previous_month_balance' => $this->getPreviousMonthBalance(),
            'previous_month_income' => $this->getPreviousMonthIncome(),
            'previous_month_expenses' => $this->getPreviousMonthExpenses(),
            'previous_month_savings' => $this->getPreviousMonthSavings(),
        ];
        
        return view('dashboard', compact('widgets', 'dashboardData'));
    }
    
    private function getMonthlyIncome()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        return Transaction::where('user_id', auth()->id())
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }
    
    private function getMonthlyExpenses()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        return Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }
    
    private function getBudgetUsage()
    {
        $result = [];
        $budgets = Budget::where('user_id', auth()->id())->with('category')->get();
        
        foreach ($budgets as $budget) {
            $spending = $budget->getCurrentSpending();
            $result[] = [
                'category' => $budget->category->name,
                'spent' => $spending,
                'limit' => $budget->amount,
                'percentage' => min(100, ($spending / $budget->amount) * 100),
                'color' => $budget->category->color,
            ];
        }
        
        return $result;
    }
    
    private function getMoneyFlow()
    {
        $result = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        
        foreach ($months as $index => $month) {
            $startDate = Carbon::now()->startOfYear()->addMonths($index)->startOfMonth();
            $endDate = Carbon::now()->startOfYear()->addMonths($index)->endOfMonth();
            
            $income = Transaction::where('user_id', auth()->id())
                ->where('type', 'income')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount');
                
            $expenses = Transaction::where('user_id', auth()->id())
                ->where('type', 'expense')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount');
                
            $result[] = [
                'month' => $month,
                'income' => $income,
                'expenses' => $expenses,
            ];
        }
        
        return $result;
    }
    
    private function getPreviousMonthBalance()
    {
        $lastMonth = Carbon::now()->subMonth()->endOfMonth();
        
        // Calculate what the balance was at the end of last month
        $currentBalance = auth()->user()->getTotalBalance();
        $thisMonthTransactions = Transaction::where('user_id', auth()->id())
            ->where('date', '>', $lastMonth)
            ->get();
            
        $thisMonthNet = 0;
        foreach ($thisMonthTransactions as $transaction) {
            if ($transaction->type === 'income') {
                $thisMonthNet += $transaction->amount;
            } else if ($transaction->type === 'expense') {
                $thisMonthNet -= $transaction->amount;
            }
        }
        
        $lastMonthBalance = $currentBalance - $thisMonthNet;
        
        return $lastMonthBalance;
    }
    
    private function getPreviousMonthIncome()
    {
        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->subMonth()->endOfMonth();
        
        return Transaction::where('user_id', auth()->id())
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }
    
    private function getPreviousMonthExpenses()
    {
        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->subMonth()->endOfMonth();
        
        return Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }
    
    private function getPreviousMonthSavings()
    {
        $lastMonth = Carbon::now()->subMonth()->endOfMonth();
        
        // Calculate what the savings balance was at the end of last month
        $currentSavings = auth()->user()->getSavingsTotal();
        $thisMonthSavingsTransactions = Transaction::where('user_id', auth()->id())
            ->whereHas('account', function($q) {
                $q->where('type', 'savings');
            })
            ->where('date', '>', $lastMonth)
            ->get();
            
        $thisMonthNet = 0;
        foreach ($thisMonthSavingsTransactions as $transaction) {
            if ($transaction->type === 'income') {
                $thisMonthNet += $transaction->amount;
            } else if ($transaction->type === 'expense') {
                $thisMonthNet -= $transaction->amount;
            }
        }
        
        $lastMonthSavings = $currentSavings - $thisMonthNet;
        
        return $lastMonthSavings;
    }
}
