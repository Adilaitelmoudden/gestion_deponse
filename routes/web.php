<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Transactions
Route::resource('transactions', TransactionController::class);
Route::post('transactions/bulk-delete', [TransactionController::class, 'bulkDelete'])->name('transactions.bulk-delete');

// Categories
Route::resource('categories', CategoryController::class);

// Budgets
Route::resource('budgets', BudgetController::class);

// Reports
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');