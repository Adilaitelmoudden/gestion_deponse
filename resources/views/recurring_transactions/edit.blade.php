@extends('layouts.app')

@section('title', 'Modifier Transaction Récurrente')
@section('header', 'Modifier la Transaction Récurrente')

@section('content')
<div class="card">
    <div class="card-header"><h5><i class="fas fa-edit"></i> Modifier</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('recurring.update', $recurring) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-control" required>
                        <option value="expense" {{ old('type', $recurring->type) == 'expense' ? 'selected' : '' }}>Dépense</option>
                        <option value="income"  {{ old('type', $recurring->type) == 'income'  ? 'selected' : '' }}>Revenu</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Catégorie *</label>
                    <select name="category_id" class="form-control" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $recurring->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Montant ({{ $currency }}) *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" class="form-control"
                           value="{{ old('amount', $recurring->amount) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fréquence *</label>
                    <select name="frequency" class="form-control" required>
                        <option value="daily"   {{ old('frequency', $recurring->frequency) == 'daily'   ? 'selected' : '' }}>Quotidien</option>
                        <option value="weekly"  {{ old('frequency', $recurring->frequency) == 'weekly'  ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="monthly" {{ old('frequency', $recurring->frequency) == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                        <option value="yearly"  {{ old('frequency', $recurring->frequency) == 'yearly'  ? 'selected' : '' }}>Annuel</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-control" value="{{ old('description', $recurring->description) }}">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de fin</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ old('end_date', $recurring->end_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                               {{ old('is_active', $recurring->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Transaction active</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
                <a href="{{ route('recurring.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
