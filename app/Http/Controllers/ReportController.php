<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $years = Transaction::select(DB::raw('YEAR(date) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
            
        return view('reports.index', compact('years'));
    }
    
    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:monthly,yearly,category',
            'year' => 'required|integer',
            'month' => 'required_if:report_type,monthly|integer|min:1|max:12'
        ]);
        
        $reportType = $request->report_type;
        $year = $request->year;
        
        if($reportType == 'monthly') {
            $month = $request->month;
            $data = $this->getMonthlyReport($year, $month);
            return view('reports.monthly', compact('data', 'year', 'month'));
        }
        elseif($reportType == 'yearly') {
            $data = $this->getYearlyReport($year);
            return view('reports.yearly', compact('data', 'year'));
        }
        elseif($reportType == 'category') {
            $data = $this->getCategoryReport($year);
            return view('reports.category', compact('data', 'year'));
        }
    }
    
    private function getMonthlyReport($year, $month)
    {
        $totalIncome = Transaction::where('type', 'income')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');
            
        $totalExpense = Transaction::where('type', 'expense')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');
            
        $expensesByCategory = Transaction::where('type', 'expense')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();
            
        $transactions = Transaction::with('category')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->get();
            
        return compact('totalIncome', 'totalExpense', 'expensesByCategory', 'transactions');
    }
    
    private function getYearlyReport($year)
    {
        $monthlyData = [];
        $totalYearIncome = 0;
        $totalYearExpense = 0;
        
        for($month = 1; $month <= 12; $month++) {
            $income = Transaction::where('type', 'income')
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('amount');
                
            $expense = Transaction::where('type', 'expense')
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('amount');
                
            $monthlyData[$month] = [
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense
            ];
            
            $totalYearIncome += $income;
            $totalYearExpense += $expense;
        }
        
        $categoriesSummary = Transaction::whereYear('date', $year)
            ->with('category')
            ->select('category_id', 'type', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id', 'type')
            ->get();
            
        return compact('monthlyData', 'totalYearIncome', 'totalYearExpense', 'categoriesSummary');
    }
    
    private function getCategoryReport($year)
    {
        $categories = Category::with(['transactions' => function($query) use ($year) {
            $query->whereYear('date', $year);
        }])->get();
        
        $totalYearIncome = Transaction::where('type', 'income')
            ->whereYear('date', $year)
            ->sum('amount');
            
        $totalYearExpense = Transaction::where('type', 'expense')
            ->whereYear('date', $year)
            ->sum('amount');
            
        return compact('categories', 'totalYearIncome', 'totalYearExpense', 'year');
    }
}