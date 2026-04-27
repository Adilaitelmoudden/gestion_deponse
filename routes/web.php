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
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserProfileController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminExportController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminStatisticsController;

// Routes d'authentification (non protégées)
Route::middleware(['guest', \App\Http\Middleware\MaintenanceMiddleware::class])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Routes protégées (authentification requise)
Route::middleware(['auth', \App\Http\Middleware\MaintenanceMiddleware::class, \App\Http\Middleware\LogActivityMiddleware::class])->group(function () {
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

    // Notifications
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

        // ── Existing ──────────────────────────────────────────────────
        Route::resource('users', UserManagementController::class);
        Route::get('users/{user}/toggle', [UserManagementController::class, 'toggleStatus'])->name('users.toggle');

        // ── 1. Admin Dashboard ────────────────────────────────────────
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // ── 2. Admin User Profile ─────────────────────────────────────
        Route::get('users/{user}/profile', [AdminUserProfileController::class, 'show'])->name('users.profile');

        // ── 3. Admin Notifications ────────────────────────────────────
        Route::get('notifications/compose',           [AdminNotificationController::class, 'compose'])->name('notifications.compose');
        Route::post('notifications/send',             [AdminNotificationController::class, 'send'])->name('notifications.send');
        Route::get('notifications/history',           [AdminNotificationController::class, 'history'])->name('notifications.history');
        Route::delete('notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');

        // ── 4. Admin Export CSV ───────────────────────────────────────
        Route::get('export',              [AdminExportController::class, 'index'])->name('export.index');
        Route::get('export/users',        [AdminExportController::class, 'exportUsers'])->name('export.users');
        Route::get('export/transactions', [AdminExportController::class, 'exportTransactions'])->name('export.transactions');

        // ── 5. Admin Settings ─────────────────────────────────────────
        Route::get('settings',              [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::put('settings',              [AdminSettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/reset',       [AdminSettingsController::class, 'reset'])->name('settings.reset');
        Route::post('settings/clear-cache', [AdminSettingsController::class, 'clearCache'])->name('settings.clear-cache');
        Route::get('settings/export',       [AdminSettingsController::class, 'export'])->name('settings.export');

        // ── 6. Journaux d'activité ────────────────────────────────────
        Route::get('activity-logs',          [AdminActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::delete('activity-logs/purge', [AdminActivityLogController::class, 'purge'])->name('activity-logs.purge');

        // ── 7. Statistiques avancées ──────────────────────────────────
        Route::get('statistics', [AdminStatisticsController::class, 'index'])->name('statistics.index');
    });
});
