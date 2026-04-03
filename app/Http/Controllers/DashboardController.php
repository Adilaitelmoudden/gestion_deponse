<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques du mois courant
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Totaux du mois
        $totalIncome = Transaction::where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
            
        $totalExpense = Transaction::where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
            
        $balance = $totalIncome - $totalExpense;
        
        // Dépenses par catégorie (pour le graphique)
        $expensesByCategory = Transaction::where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();
            
        // Revenus par catégorie
        $incomeByCategory = Transaction::where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();
        
        // Dernières transactions
        $recentTransactions = Transaction::with('category')
            ->latest('date')
            ->take(10)
            ->get();
        
        // Top 5 des catégories les plus dépensières
        $topExpenseCategories = Transaction::where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();
        
        // Budget vs Réel
        $budgets = Budget::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->with('category')
            ->get();
            
        foreach($budgets as $budget) {
            $budget->spent = Transaction::where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->sum('amount');
            $budget->percentage = $budget->amount > 0 ? ($budget->spent / $budget->amount) * 100 : 0;
        }
        
        // Statistiques de l'année (pour l'évolution)
        $monthlyStats = [];
        for($i = 1; $i <= 12; $i++) {
            $monthlyStats[$i] = [
                'income' => Transaction::where('type', 'income')
                    ->whereMonth('date', $i)
                    ->whereYear('date', $currentYear)
                    ->sum('amount'),
                'expense' => Transaction::where('type', 'expense')
                    ->whereMonth('date', $i)
                    ->whereYear('date', $currentYear)
                    ->sum('amount')
            ];
        }
        
        return view('dashboard', compact(
            'totalIncome', 'totalExpense', 'balance',
            'expensesByCategory', 'incomeByCategory',
            'recentTransactions', 'topExpenseCategories',
            'budgets', 'monthlyStats', 'currentMonth', 'currentYear'
        ));
    }
}