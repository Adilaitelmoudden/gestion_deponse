<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use App\Models\SavingsGoal;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatisticsController extends Controller
{
    public function index()
    {
        // ── 1. KPIs globaux ─────────────────────────────────────────
        $kpis = $this->getKpis();

        // ── 2. Évolution revenus/dépenses — 12 mois ─────────────────
        $monthlyChart = $this->getMonthlyChart();

        // ── 3. Répartition par type de transaction (donut) ──────────
        $typeDonut = [
            'income'  => Transaction::where('type', 'income')->count(),
            'expense' => Transaction::where('type', 'expense')->count(),
        ];

        // ── 4. Top 10 catégories par montant total ───────────────────
        $topCategories = Category::select(
                'categories.name',
                'categories.color',
                'categories.icon',
                DB::raw('COUNT(transactions.id) as tx_count'),
                DB::raw('SUM(transactions.amount) as total_amount')
            )
            ->join('transactions', 'categories.id', '=', 'transactions.category_id')
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        // ── 5. Utilisateurs — croissance mensuelle (12 mois) ─────────
        $userGrowthChart = $this->getUserGrowthChart();

        // ── 6. Taux d'activité : utilisateurs ayant eu ≥1 tx ce mois ─
        $activeThisMonth = Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->distinct('user_id')
            ->count('user_id');

        $totalUsers = User::count();
        $activityRate = $totalUsers > 0
            ? round(($activeThisMonth / $totalUsers) * 100, 1)
            : 0;

        // ── 7. Moyenne de transactions / utilisateur ─────────────────
        $avgTxPerUser = $totalUsers > 0
            ? round(Transaction::count() / $totalUsers, 1)
            : 0;

        // ── 8. Montant moyen par transaction ─────────────────────────
        $avgTxAmount = round(Transaction::avg('amount') ?? 0, 2);

        // ── 9. Jour de la semaine le plus actif ──────────────────────
        $busyDay = $this->getBusyDay();

        // ── 10. Heure de pointe ──────────────────────────────────────
        $peakHour = $this->getPeakHour();

        // ── 11. Budgets — taux de dépassement ────────────────────────
        $budgetStats = $this->getBudgetStats();

        // ── 12. Objectifs d'épargne — taux de complétion ─────────────
        $savingsStats = $this->getSavingsStats();

        // ── 13. Activité des 30 derniers jours (heatmap data) ────────
        $activityHeatmap = $this->getActivityHeatmap();

        // ── 14. Top 5 utilisateurs — montant le plus dépensé ─────────
        $topSpenders = User::select('users.id', 'users.name', 'users.email',
                DB::raw('SUM(transactions.amount) as total_spent'),
                DB::raw('COUNT(transactions.id) as tx_count')
            )
            ->join('transactions', 'users.id', '=', 'transactions.user_id')
            ->where('transactions.type', 'expense')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // ── 15. Logs d'activité — 7 derniers jours par module ────────
        $logsByModule = [];
        if (class_exists(\App\Models\ActivityLog::class) && \Schema::hasTable('activity_logs')) {
            $logsByModule = ActivityLog::select('module', DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('module')
                ->orderByDesc('count')
                ->get();
        }

        return view('admin.statistics.index', compact(
            'kpis',
            'monthlyChart',
            'typeDonut',
            'topCategories',
            'userGrowthChart',
            'activeThisMonth',
            'activityRate',
            'avgTxPerUser',
            'avgTxAmount',
            'busyDay',
            'peakHour',
            'budgetStats',
            'savingsStats',
            'activityHeatmap',
            'topSpenders',
            'logsByModule'
        ));
    }

    // ── Helpers privés ───────────────────────────────────────────────

    private function getKpis(): array
    {
        $now       = now();
        $lastMonth = now()->subMonth();

        $txThisMonth  = Transaction::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $txLastMonth  = Transaction::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count();
        $txGrowth     = $txLastMonth > 0 ? round((($txThisMonth - $txLastMonth) / $txLastMonth) * 100, 1) : 0;

        $revThisMonth = Transaction::where('type','income')->whereMonth('date',$now->month)->whereYear('date',$now->year)->sum('amount');
        $revLastMonth = Transaction::where('type','income')->whereMonth('date',$lastMonth->month)->whereYear('date',$lastMonth->year)->sum('amount');
        $revGrowth    = $revLastMonth > 0 ? round((($revThisMonth - $revLastMonth) / $revLastMonth) * 100, 1) : 0;

        $expThisMonth = Transaction::where('type','expense')->whereMonth('date',$now->month)->whereYear('date',$now->year)->sum('amount');
        $expLastMonth = Transaction::where('type','expense')->whereMonth('date',$lastMonth->month)->whereYear('date',$lastMonth->year)->sum('amount');
        $expGrowth    = $expLastMonth > 0 ? round((($expThisMonth - $expLastMonth) / $expLastMonth) * 100, 1) : 0;

        $usersThisMonth = User::whereMonth('created_at',$now->month)->whereYear('created_at',$now->year)->count();
        $usersLastMonth = User::whereMonth('created_at',$lastMonth->month)->whereYear('created_at',$lastMonth->year)->count();
        $userGrowth     = $usersLastMonth > 0 ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 1) : 0;

        return compact(
            'txThisMonth','txGrowth',
            'revThisMonth','revGrowth',
            'expThisMonth','expGrowth',
            'usersThisMonth','userGrowth'
        );
    }

    private function getMonthlyChart(): array
    {
        $labels = $income = $expense = $net = [];

        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labels[]  = $m->format('M Y');
            $inc       = (float) Transaction::where('type','income')->whereYear('date',$m->year)->whereMonth('date',$m->month)->sum('amount');
            $exp       = (float) Transaction::where('type','expense')->whereYear('date',$m->year)->whereMonth('date',$m->month)->sum('amount');
            $income[]  = $inc;
            $expense[] = $exp;
            $net[]     = round($inc - $exp, 2);
        }

        return compact('labels','income','expense','net');
    }

    private function getUserGrowthChart(): array
    {
        $labels = $counts = $cumulative = [];
        $total  = 0;

        for ($i = 11; $i >= 0; $i--) {
            $m        = now()->subMonths($i);
            $labels[] = $m->format('M Y');
            $cnt      = User::whereYear('created_at',$m->year)->whereMonth('created_at',$m->month)->count();
            $total   += $cnt;
            $counts[]     = $cnt;
            $cumulative[] = $total;
        }

        return compact('labels','counts','cumulative');
    }

    private function getBusyDay(): string
    {
        $row = Transaction::select(DB::raw('DAYOFWEEK(date) as dow, COUNT(*) as cnt'))
            ->groupBy('dow')
            ->orderByDesc('cnt')
            ->first();

        if (!$row) return 'N/A';

        $days = ['','Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
        return $days[$row->dow] ?? 'N/A';
    }

    private function getPeakHour(): string
    {
        $row = Transaction::select(DB::raw('HOUR(created_at) as hr, COUNT(*) as cnt'))
            ->groupBy('hr')
            ->orderByDesc('cnt')
            ->first();

        return $row ? sprintf('%02d:00 – %02d:59', $row->hr, $row->hr) : 'N/A';
    }

    private function getBudgetStats(): array
    {
        $total    = Budget::count();
        $exceeded = 0;

        if ($total > 0) {
            $budgets = Budget::with(['category'])->get();
            foreach ($budgets as $b) {
                $spent = Transaction::where('category_id', $b->category_id)
                    ->where('type', 'expense')
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->sum('amount');
                if ($spent > $b->amount) $exceeded++;
            }
        }

        return [
            'total'    => $total,
            'exceeded' => $exceeded,
            'rate'     => $total > 0 ? round(($exceeded / $total) * 100, 1) : 0,
        ];
    }

    private function getSavingsStats(): array
    {
        $total     = SavingsGoal::count();
        $completed = SavingsGoal::whereColumn('current_amount', '>=', 'target_amount')->count();

        return [
            'total'     => $total,
            'completed' => $completed,
            'rate'      => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
        ];
    }

    private function getActivityHeatmap(): array
    {
        // Retourne le nombre de transactions par jour sur les 30 derniers jours
        $rows = Transaction::select(DB::raw('DATE(created_at) as day, COUNT(*) as cnt'))
            ->where('created_at', '>=', now()->subDays(29))
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $d      = now()->subDays($i)->format('Y-m-d');
            $data[] = ['date' => $d, 'count' => $rows[$d]->cnt ?? 0];
        }

        return $data;
    }
}
