@extends('layouts.app')

@section('title', 'Catégories')
@section('header', 'Gestion des Catégories')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-tags"></i> Liste des Catégories</h5>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Catégorie
                </a>
            </div>
            <div class="card-body">
                <!-- Dépenses Categories -->
                <h5 class="text-danger mb-3">
                    <i class="fas fa-arrow-down"></i> Dépenses
                </h5>
                <div class="row mb-4">
                    @foreach($expenseCategories as $category)
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center" style="border-top: 4px solid {{ $category->color }}">
                                <i class="{{ $category->icon }} fa-2x mb-2" style="color: {{ $category->color }}"></i>
                                <h6>{{ $category->name }}</h6>
                                <small class="text-muted">
                                    {{ $category->transactions_count ?? 0 }} transaction(s)
                                </small>
                                <div class="mt-2">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$category->is_default)
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette catégorie ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Revenus Categories -->
                <h5 class="text-success mb-3 mt-4">
                    <i class="fas fa-arrow-up"></i> Revenus
                </h5>
                <div class="row">
                    @foreach($incomeCategories as $category)
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center" style="border-top: 4px solid {{ $category->color }}">
                                <i class="{{ $category->icon }} fa-2x mb-2" style="color: {{ $category->color }}"></i>
                                <h6>{{ $category->name }}</h6>
                                <small class="text-muted">
                                    {{ $category->transactions_count ?? 0 }} transaction(s)
                                </small>
                                <div class="mt-2">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$category->is_default)
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette catégorie ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection