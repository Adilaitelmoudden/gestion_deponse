<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ── User stats ──────────────────────────────────────
        $totalUsers    = User::count();
        $activeUsers   = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $newThisMonth  = User::whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year)
                             ->count();

        // ── Transaction stats ────────────────────────────────
        $totalTransactions = Transaction::count();
        $totalIncome       = Transaction::where('type', 'income')->sum('amount');
        $totalExpense      = Transaction::where('type', 'expense')->sum('amount');

        // ── Revenue vs Expense — last 12 months (bar chart) ──
        $revenueExpenseChart = $this->getRevenueExpenseChart();

        // ── New registrations — last 6 months (line chart) ───
        $registrationsChart = $this->getRegistrationsChart();

        // ── Top 5 most active users ──────────────────────────
        $topUsers = User::withCount('transactions')
                        ->orderByDesc('transactions_count')
                        ->limit(5)
                        ->get();

        // ── Top 5 expense categories ─────────────────────────
        $topCategories = Category::select('categories.*', DB::raw('SUM(transactions.amount) as total_spent'))
            ->join('transactions', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.type', 'expense')
            ->groupBy('categories.id')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // ── Recent users ─────────────────────────────────────
        $recentUsers = User::latest()->limit(5)->get();

        // ── Recent transactions (all users) ──────────────────
        $recentTransactions = Transaction::with(['user', 'category'])
                                          ->latest()
                                          ->limit(10)
                                          ->get();

        return view('admin.dashboard.index', compact(
            'totalUsers', 'activeUsers', 'inactiveUsers', 'newThisMonth',
            'totalTransactions', 'totalIncome', 'totalExpense',
            'revenueExpenseChart', 'registrationsChart',
            'topUsers', 'topCategories', 'recentUsers', 'recentTransactions'
        ));
    }

    private function getRevenueExpenseChart(): array
    {
        $labels  = [];
        $income  = [];
        $expense = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $income[] = Transaction::where('type', 'income')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');

            $expense[] = Transaction::where('type', 'expense')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
        }

        return compact('labels', 'income', 'expense');
    }

    private function getRegistrationsChart(): array
    {
        $labels = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $month    = now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $counts[] = User::whereYear('created_at', $month->year)
                            ->whereMonth('created_at', $month->month)
                            ->count();
        }

        return compact('labels', 'counts');
    }
}
