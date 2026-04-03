<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('type')->orderBy('name')->get();
        $expenseCategories = Category::where('type', 'expense')->get();
        $incomeCategories = Category::where('type', 'income')->get();
        
        return view('categories.index', compact('categories', 'expenseCategories', 'incomeCategories'));
    }
    
    public function create()
    {
        return view('categories.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'color' => 'nullable|string|max:7',
            'type' => 'required|in:income,expense',
            'icon' => 'nullable|string|max:50'
        ]);
        
        $validated['is_default'] = false;
        $validated['user_id'] = null;
        
        Category::create($validated);
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie créée avec succès!');
    }
    
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }
    
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'color' => 'nullable|string|max:7',
            'type' => 'required|in:income,expense',
            'icon' => 'nullable|string|max:50'
        ]);
        
        $category->update($validated);
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie modifiée avec succès!');
    }
    
    public function destroy(Category $category)
    {
        // Vérifier si la catégorie a des transactions
        if($category->transactions()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des transactions!');
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès!');
    }
}