<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        
        // Catégories par défaut + catégories personnelles de l'utilisateur
        $expenseCategories = Category::where('type', 'expense')
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('is_default', true);
            })
            ->withCount('transactions')
            ->get();
            
        $incomeCategories = Category::where('type', 'income')
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('is_default', true);
            })
            ->withCount('transactions')
            ->get();
        
        return view('categories.index', compact('expenseCategories', 'incomeCategories'));
    }
    
    public function create()
    {
        return view('categories.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
            'type' => 'required|in:income,expense',
            'icon' => 'nullable|string|max:50'
        ]);
        
        $validated['user_id'] = session('user_id');
        $validated['is_default'] = false;
        
        // Vérifier l'unicité pour l'utilisateur
        $exists = Category::where('user_id', session('user_id'))
            ->where('name', $validated['name'])
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'Vous avez déjà une catégorie avec ce nom.')->withInput();
        }
        
        Category::create($validated);
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie créée avec succès!');
    }
    
    public function edit(Category $category)
    {
        // Vérifier que la catégorie appartient à l'utilisateur
        if ($category->user_id != session('user_id') && !$category->is_default) {
            abort(403, 'Accès non autorisé.');
        }
        
        // Les catégories par défaut ne peuvent pas être modifiées
        if ($category->is_default) {
            return redirect()->route('categories.index')
                ->with('error', 'Les catégories par défaut ne peuvent pas être modifiées.');
        }
        
        return view('categories.edit', compact('category'));
    }
    
    public function update(Request $request, Category $category)
    {
        // Vérifier que la catégorie appartient à l'utilisateur
        if ($category->user_id != session('user_id')) {
            abort(403, 'Accès non autorisé.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
            'type' => 'required|in:income,expense',
            'icon' => 'nullable|string|max:50'
        ]);
        
        // Vérifier l'unicité
        $exists = Category::where('user_id', session('user_id'))
            ->where('name', $validated['name'])
            ->where('id', '!=', $category->id)
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'Vous avez déjà une catégorie avec ce nom.')->withInput();
        }
        
        $category->update($validated);
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie modifiée avec succès!');
    }
    
    public function destroy(Category $category)
    {
        // Vérifier que la catégorie appartient à l'utilisateur
        if ($category->user_id != session('user_id')) {
            abort(403, 'Accès non autorisé.');
        }
        
        // Vérifier si la catégorie a des transactions
        if($category->transactions()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient ' . $category->transactions()->count() . ' transaction(s)!');
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès!');
    }
}