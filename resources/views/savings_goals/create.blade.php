@extends('layouts.app')

@section('title', 'Nouvel Objectif')
@section('header', 'Créer un Objectif d\'Épargne')

@section('content')
<div class="card">
    <div class="card-header"><h5><i class="fas fa-plus"></i> Nouvel Objectif d'Épargne</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('savings_goals.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nom de l'objectif *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Ex: Vacances, Voiture, Urgences..." required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Montant cible ({{ $currency }}) *</label>
                    <input type="number" name="target_amount" step="0.01" min="1"
                           class="form-control @error('target_amount') is-invalid @enderror"
                           value="{{ old('target_amount') }}" required>
                    @error('target_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Épargne initiale ({{ $currency }})</label>
                    <input type="number" name="current_amount" step="0.01" min="0"
                           class="form-control" value="{{ old('current_amount', 0) }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Date limite</label>
                <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                       value="{{ old('deadline') }}">
                @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="Pourquoi cet objectif ?">{{ old('description') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Créer</button>
                <a href="{{ route('savings_goals.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
