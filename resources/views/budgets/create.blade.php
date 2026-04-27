@extends('layouts.app')

@section('title', 'Nouveau Budget')
@section('header', 'Définir un Budget')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus-circle"></i> Nouveau Budget Mensuel</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('budgets.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Catégorie *</label>
                        <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    <i class="{{ $category->icon }}"></i> {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Montant maximum ({{ $currency }}) *</label>
                        <input type="number" step="0.01" name="amount" id="amount" 
                               class="form-control @error('amount') is-invalid @enderror" 
                               value="{{ old('amount') }}" placeholder="0.00" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="month" class="form-label">Mois *</label>
                                <select name="month" id="month" class="form-control @error('month') is-invalid @enderror" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ old('month', date('n')) == $m ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                @error('month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="year" class="form-label">Année *</label>
                                <select name="year" id="year" class="form-control @error('year') is-invalid @enderror" required>
                                    @for($y = date('Y')-1; $y <= date('Y')+2; $y++)
                                        <option value="{{ $y }}" {{ old('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Le budget vous aidera à suivre vos dépenses pour cette catégorie.
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer le budget
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection