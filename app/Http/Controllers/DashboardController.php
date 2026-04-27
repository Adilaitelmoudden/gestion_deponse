<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use App\Models\User;
use App\Models\SavingsGoal;
use App\Models\RecurringTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\CurrencyService;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        
        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
            
        $totalExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
            
        $balance = $totalIncome - $totalExpense;

        // ── Currency conversion (MAD → selected currency) ──────────
        $totalIncome  = CurrencyService::convert($totalIncome);
        $totalExpense = CurrencyService::convert($totalExpense);
        $balance      = CurrencyService::convert($balance);
        
        $expensesByCategory = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();
            
        $incomeByCategory = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();
        
        $recentTransactions = Transaction::where('user_id', $userId)
            ->with('category')
            ->latest('date')
            ->take(10)
            ->get();
        
        $topExpenseCategories = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();
        
        $budgets = Budget::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->with('category')
            ->get();
            
        foreach($budgets as $budget) {
            $budget->spent = Transaction::where('user_id', $userId)
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->sum('amount');
            $budget->percentage = $budget->amount > 0 ? ($budget->spent / $budget->amount) * 100 : 0;
            $budget->remaining  = $budget->amount - $budget->spent;
            // Convert to selected currency
            $budget->amount    = CurrencyService::convert($budget->amount);
            $budget->spent     = CurrencyService::convert($budget->spent);
            $budget->remaining = CurrencyService::convert($budget->remaining);

            // NOUVEAU — burn rate : ratio (% dépensé) / (% du mois écoulé)
            $dayFraction = now()->daysInMonth > 0 ? now()->day / now()->daysInMonth : 1;
            $budget->burnRate = $dayFraction > 0 ? ($budget->percentage / 100) / $dayFraction : 0;
        }
        
        $monthlyStats = [];
        for($i = 1; $i <= 12; $i++) {
            $monthlyStats[$i] = [
                'income'  => CurrencyService::convert(Transaction::where('user_id', $userId)->where('type', 'income')
                    ->whereMonth('date', $i)->whereYear('date', $currentYear)->sum('amount')),
                'expense' => CurrencyService::convert(Transaction::where('user_id', $userId)->where('type', 'expense')
                    ->whereMonth('date', $i)->whereYear('date', $currentYear)->sum('amount')),
            ];
        }

        // NOUVEAU — Sparklines : dépenses & revenus des 7 derniers jours
        $last7Days = [];
        for ($d = 6; $d >= 0; $d--) {
            $day = now()->subDays($d)->format('Y-m-d');
            $last7Days[] = [
                'label'   => now()->subDays($d)->format('d/m'),
                'income'  => CurrencyService::convert(Transaction::where('user_id', $userId)->where('type', 'income')
                    ->whereDate('date', $day)->sum('amount')),
                'expense' => CurrencyService::convert(Transaction::where('user_id', $userId)->where('type', 'expense')
                    ->whereDate('date', $day)->sum('amount')),
            ];
        }

        // NOUVEAU — Factures à venir dans les 7 prochains jours
        $upcomingBills      = collect();
        $upcomingBillsTotal = 0;
        try {
            $upcomingBills = RecurringTransaction::where('user_id', $userId)
                ->where('is_active', true)
                ->whereBetween('next_due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
                ->with('category')
                ->orderBy('next_due_date')
                ->get();
            $upcomingBillsTotal = CurrencyService::convert($upcomingBills->sum('amount'));
        } catch (\Exception $e) {
            // colonne optionnelle, on ignore
        }

        // NOUVEAU — Score de santé financière (0-100)
        $healthScore = $this->computeHealthScore($userId, $totalIncome, $balance, $budgets);

        // NOUVEAU — Insights personnalisés
        $insights = $this->getInsights($userId, $totalIncome, $totalExpense, $budgets);

        // Convert collection amounts to selected currency
        $expensesByCategory->each(fn($c) => $c->total = CurrencyService::convert($c->total));
        $incomeByCategory->each(fn($c) => $c->total = CurrencyService::convert($c->total));
        $topExpenseCategories->each(fn($c) => $c->total = CurrencyService::convert($c->total));
        $recentTransactions->each(fn($t) => $t->amount = CurrencyService::convert($t->amount));
        $upcomingBills->each(fn($b) => $b->amount = CurrencyService::convert($b->amount));

        return view('dashboard', compact(
            'totalIncome', 'totalExpense', 'balance',
            'expensesByCategory', 'incomeByCategory',
            'recentTransactions', 'topExpenseCategories',
            'budgets', 'monthlyStats', 'currentMonth', 'currentYear', 'user',
            'last7Days', 'upcomingBills', 'upcomingBillsTotal',
            'healthScore', 'insights'
        ));
    }

    // NOUVEAU — Score de santé financière
    private function computeHealthScore($userId, $totalIncome, $balance, $budgets): int
    {
        $score = 0;

        // 40 pts — taux d'épargne
        $savingsRate = $totalIncome > 0 ? ($balance / $totalIncome) * 100 : 0;
        if ($savingsRate >= 20)     $score += 40;
        elseif ($savingsRate >= 10) $score += 20;
        elseif ($savingsRate > 0)   $score += 10;

        // 40 pts — budgets respectés
        if ($budgets->count() > 0) {
            $respected = $budgets->filter(fn($b) => $b->percentage <= 100)->count();
            $score += (int) round(40 * ($respected / $budgets->count()));
        } else {
            $score += 20;
        }

        // 20 pts — objectif d'épargne actif
        if (SavingsGoal::where('user_id', $userId)->where('current_amount', '>', 0)->exists()) {
            $score += 20;
        }

        return min(100, max(0, $score));
    }

    // NOUVEAU — Insights intelligents (max 3)
    private function getInsights($userId, $totalIncome, $totalExpense, $budgets): array
    {
        $insights    = [];
        $prevMonth   = now()->subMonth()->month;
        $prevYear    = now()->subMonth()->year;
        $prevExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('date', $prevMonth)
            ->whereYear('date', $prevYear)
            ->sum('amount');

        if ($prevExpense > 0 && $totalExpense > 0) {
            $delta = (($totalExpense - $prevExpense) / $prevExpense) * 100;
            if ($delta >= 20) {
                $insights[] = ['type'=>'danger','icon'=>'fa-arrow-trend-up',
                    'text'=>'Vos dépenses ont augmenté de '.number_format($delta,0).'% par rapport au mois dernier.'];
            } elseif ($delta <= -10) {
                $insights[] = ['type'=>'success','icon'=>'fa-arrow-trend-down',
                    'text'=>'Bravo ! Vos dépenses ont diminué de '.number_format(abs($delta),0).'% ce mois-ci.'];
            }
        }

        foreach ($budgets as $budget) {
            if ($budget->percentage >= 80 && $budget->percentage < 100) {
                $insights[] = ['type'=>'warning','icon'=>'fa-triangle-exclamation',
                    'text'=>'Budget "'.(optional($budget->category)->name ?? '—').'" utilisé à '.number_format($budget->percentage,0).'%. Attention !'];
            } elseif ($budget->percentage >= 100) {
                $insights[] = ['type'=>'danger','icon'=>'fa-circle-exclamation',
                    'text'=>'Budget "'.(optional($budget->category)->name ?? '—').'" dépassé de '.number_format($budget->remaining * -1,2).' ' . Cache::get('admin_system_settings', [])['default_currency'] ?? 'MAD' . '.'];
            }
        }

        $savingsRate = $totalIncome > 0 ? ($totalIncome - $totalExpense) / $totalIncome * 100 : 0;
        if ($totalIncome > 0 && $savingsRate >= 20) {
            $insights[] = ['type'=>'success','icon'=>'fa-piggy-bank',
                'text'=>'Excellent taux d\'épargne de '.number_format($savingsRate,1).'% ce mois-ci. Continuez !'];
        } elseif ($totalIncome > 0 && $savingsRate < 0) {
            $insights[] = ['type'=>'danger','icon'=>'fa-wallet',
                'text'=>'Attention : vos dépenses dépassent vos revenus ce mois-ci.'];
        }

        return array_slice($insights, 0, 3);
    }
}
