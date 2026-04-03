<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $budgets = Budget::where('month', $month)
            ->where('year', $year)
            ->with('category')
            ->get();
        
        // Calculer les dépenses réelles pour chaque budget
        foreach($budgets as $budget) {
            $budget->spent = Transaction::where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');
            $budget->percentage = $budget->amount > 0 ? ($budget->spent / $budget->amount) * 100 : 0;
            $budget->remaining = $budget->amount - $budget->spent;
        }
        
        $categories = Category::where('type', 'expense')->get();
        $availableMonths = range(1, 12);
        $availableYears = range(now()->year - 2, now()->year + 1);
        
        return view('budgets.index', compact('budgets', 'categories', 'month', 'year', 'availableMonths', 'availableYears'));
    }
    
    public function create()
    {
        $categories = Category::where('type', 'expense')->get();
        return view('budgets.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100'
        ]);
        
        // Vérifier si un budget existe déjà
        $existing = Budget::where('category_id', $validated['category_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();
            
        if($existing) {
            return redirect()->back()
                ->with('error', 'Un budget pour cette catégorie et cette période existe déjà!')
                ->withInput();
        }
        
        Budget::create($validated);
        
        return redirect()->route('budgets.index', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('success', 'Budget créé avec succès!');
    }
    
    public function edit(Budget $budget)
    {
        $categories = Category::where('type', 'expense')->get();
        return view('budgets.edit', compact('budget', 'categories'));
    }
    
    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);
        
        $budget->update($validated);
        
        return redirect()->route('budgets.index', ['month' => $budget->month, 'year' => $budget->year])
            ->with('success', 'Budget modifié avec succès!');
    }
    
    public function destroy(Budget $budget)
    {
        $month = $budget->month;
        $year = $budget->year;
        $budget->delete();
        
        return redirect()->route('budgets.index', ['month' => $month, 'year' => $year])
            ->with('success', 'Budget supprimé avec succès!');
    }
}