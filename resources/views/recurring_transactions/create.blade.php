@extends('layouts.app')

@section('title', 'Nouvelle Transaction Récurrente')
@section('header', 'Nouvelle Transaction Récurrente')

@section('content')
<div class="card">
    <div class="card-header"><h5><i class="fas fa-redo"></i> Créer une Transaction Récurrente</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('recurring.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Dépense</option>
                        <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Revenu</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Catégorie *</label>
                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                        <option value="">-- Choisir --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->type == 'income' ? 'Revenu' : 'Dépense' }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Montant ({{ $currency }}) *</label>
                    <input type="number" name="amount" step="0.01" min="0.01"
                           class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount') }}" required>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fréquence *</label>
                    <select name="frequency" class="form-control @error('frequency') is-invalid @enderror" required>
                        <option value="daily"   {{ old('frequency') == 'daily'   ? 'selected' : '' }}>Quotidien</option>
                        <option value="weekly"  {{ old('frequency') == 'weekly'  ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }} selected>Mensuel</option>
                        <option value="yearly"  {{ old('frequency') == 'yearly'  ? 'selected' : '' }}>Annuel</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-control"
                       value="{{ old('description') }}" placeholder="Ex: Loyer, Abonnement Netflix...">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de début *</label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                           value="{{ old('start_date', now()->toDateString()) }}" required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de fin (optionnel)</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ old('end_date') }}">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Créer</button>
                <a href="{{ route('recurring.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
