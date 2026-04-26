<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AdminUserProfileController extends Controller
{
    public function show(User $user)
    {
        // ── Financial stats ───────────────────────────────────
        $totalIncome  = $user->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $user->transactions()->where('type', 'expense')->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        // ── Last 6 months chart ───────────────────────────────
        $chartLabels  = [];
        $chartIncome  = [];
        $chartExpense = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->format('M Y');

            $chartIncome[] = $user->transactions()
                ->where('type', 'income')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');

            $chartExpense[] = $user->transactions()
                ->where('type', 'expense')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
        }

        // ── Expenses by category ──────────────────────────────
        $expenseByCategory = $user->transactions()
            ->with('category')
            ->where('type', 'expense')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        // ── Last 10 transactions ──────────────────────────────
        $recentTransactions = $user->transactions()
            ->with('category')
            ->latest('date')
            ->limit(10)
            ->get();

        return view('admin.users.profile', compact(
            'user',
            'totalIncome', 'totalExpense', 'balance',
            'chartLabels', 'chartIncome', 'chartExpense',
            'expenseByCategory',
            'recentTransactions'
        ));
    }
}
