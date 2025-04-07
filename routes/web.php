<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\WidgetController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Auth::routes();

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Transactions
    Route::resource('transactions', TransactionController::class);
    
    // Accounts
    Route::resource('accounts', AccountController::class);
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Budgets
    Route::resource('budgets', BudgetController::class)->except(['create', 'edit', 'show']);
    
    // Goals
    Route::resource('goals', GoalController::class);
    Route::post('/goals/{goal}/contribute', [GoalController::class, 'contribute'])->name('goals.contribute');
    
    // Widgets
    Route::resource('widgets', WidgetController::class)->except(['create', 'edit', 'show']);
    Route::post('/widgets/reorder', [WidgetController::class, 'reorder'])->name('widgets.reorder');
});