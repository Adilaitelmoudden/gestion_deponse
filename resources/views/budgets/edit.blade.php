@extends('layouts.app')

@section('title', 'Modifier Budget')
@section('header', 'Modifier le Budget')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Modifier le Budget</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('budgets.update', $budget) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Catégorie</label>
                        <input type="text" class="form-control" value="{{ $budget->category->name }}" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Montant maximum ({{ $currency }}) *</label>
                        <input type="number" step="0.01" name="amount" id="amount" 
                               class="form-control @error('amount') is-invalid @enderror" 
                               value="{{ old('amount', $budget->amount) }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-chart-line"></i>
                        Période : {{ DateTime::createFromFormat('!m', $budget->month)->format('F') }} {{ $budget->year }}
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('budgets.index', ['month' => $budget->month, 'year' => $budget->year]) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection