<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('category');
        
        // Filtres
        if($request->filled('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        if($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        
        if($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        if($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('amount', 'like', '%' . $request->search . '%');
        }
        
        $transactions = $query->latest('date')->paginate(15);
        $categories = Category::all();
        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');
        
        return view('transactions.index', compact('transactions', 'categories', 'totalIncome', 'totalExpense'));
    }
    
    public function create()
    {
        $categories = Category::all();
        return view('transactions.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'date' => 'required|date',
            'type' => 'required|in:income,expense'
        ]);
        
        Transaction::create($validated);
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction ajoutée avec succès!');
    }
    
    public function show(Transaction $transaction)
    {
        return view('transactions.show', compact('transaction'));
    }
    
    public function edit(Transaction $transaction)
    {
        $categories = Category::all();
        return view('transactions.edit', compact('transaction', 'categories'));
    }
    
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'date' => 'required|date',
            'type' => 'required|in:income,expense'
        ]);
        
        $transaction->update($validated);
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction modifiée avec succès!');
    }
    
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction supprimée avec succès!');
    }
    
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transactions,id'
        ]);
        
        Transaction::whereIn('id', $request->ids)->delete();
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transactions supprimées avec succès!');
    }
}