<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\Admin\UserManagementController;

// Routes d'authentification (non protégées)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Routes protégées (authentification requise)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions
    Route::resource('transactions', TransactionController::class);
    Route::post('transactions/bulk-delete', [TransactionController::class, 'bulkDelete'])->name('transactions.bulk-delete');

    // Categories — json AVANT resource pour éviter conflit avec {category}
    Route::get('categories/json', [CategoryController::class, 'jsonList'])->name('categories.json');
    Route::resource('categories', CategoryController::class);

    // Budgets
    Route::resource('budgets', BudgetController::class);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // NEW: Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    // Savings Goals
    Route::resource('savings_goals', SavingsGoalController::class);
    Route::post('savings_goals/{savings_goal}/deposit', [SavingsGoalController::class, 'deposit'])->name('savings_goals.deposit');
    Route::post('savings_goals/{savings_goal}/withdraw', [SavingsGoalController::class, 'withdraw'])->name('savings_goals.withdraw');

    // Recurring Transactions
    Route::resource('recurring_transactions', RecurringTransactionController::class);

    // AI Assistant
    Route::get('assistant', [AiAssistantController::class, 'index'])->name('assistant.index');
    Route::post('assistant/chat', [AiAssistantController::class, 'chat'])->name('assistant.chat');

    // Tags
    Route::resource('tags', TagController::class);

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::get('users/{user}/toggle', [UserManagementController::class, 'toggleStatus'])->name('users.toggle');
    });
});
