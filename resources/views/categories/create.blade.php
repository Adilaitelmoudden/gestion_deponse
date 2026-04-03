@extends('layouts.app')

@section('title', 'Nouvelle Catégorie')
@section('header', 'Ajouter une Catégorie')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus-circle"></i> Nouvelle Catégorie</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom *</label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Type *</label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Dépense</option>
                            <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Revenu</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="color" class="form-label">Couleur</label>
                        <input type="color" name="color" id="color" 
                               class="form-control @error('color') is-invalid @enderror" 
                               value="{{ old('color', '#6c757d') }}">
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="icon" class="form-label">Icône</label>
                        <div class="input-group">
                            <input type="text" name="icon" id="icon" 
                                   class="form-control @error('icon') is-invalid @enderror" 
                                   value="{{ old('icon', 'fa-tag') }}" placeholder="fa-tag">
                            <span class="input-group-text">
                                <i class="{{ old('icon', 'fa-tag') }}" id="iconPreview"></i>
                            </span>
                        </div>
                        <small class="text-muted">Font Awesome 6 (ex: fa-car, fa-home, fa-shopping-cart)</small>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('icon').addEventListener('input', function() {
    document.getElementById('iconPreview').className = this.value;
});
</script>
@endpush
@endsection