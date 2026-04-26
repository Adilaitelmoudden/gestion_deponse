@extends('layouts.app')

@section('title', 'Modifier Objectif')
@section('header', 'Modifier l\'Objectif d\'Épargne')

@section('content')
<div class="card">
    <div class="card-header"><h5><i class="fas fa-edit"></i> Modifier l'Objectif</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('savings_goals.update', $savingsGoal) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nom de l'objectif *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $savingsGoal->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Montant cible (DH) *</label>
                <input type="number" name="target_amount" step="0.01" min="1"
                       class="form-control @error('target_amount') is-invalid @enderror"
                       value="{{ old('target_amount', $savingsGoal->target_amount) }}" required>
                @error('target_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Date limite</label>
                <input type="date" name="deadline" class="form-control"
                       value="{{ old('deadline', $savingsGoal->deadline?->format('Y-m-d')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $savingsGoal->description) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
                <a href="{{ route('savings_goals.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
