@extends('layouts.app')

@section('title', 'Modifier Transaction')
@section('header', 'Modifier la Transaction')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Modifier la Transaction</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('transactions.update', $transaction) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Type *</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="radio" name="type" id="type_expense" value="expense" 
                                       class="form-check-input" {{ $transaction->type == 'expense' ? 'checked' : '' }} required>
                                <label for="type_expense" class="form-check-label text-danger">
                                    <i class="fas fa-arrow-down"></i> Dépense
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="type" id="type_income" value="income" 
                                       class="form-check-input" {{ $transaction->type == 'income' ? 'checked' : '' }}>
                                <label for="type_income" class="form-check-label text-success">
                                    <i class="fas fa-arrow-up"></i> Revenu
                                </label>
                            </div>
                        </div>
                        @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Catégorie *</label>
                        <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        data-type="{{ $category->type }}"
                                        {{ $transaction->category_id == $category->id ? 'selected' : '' }}>
                                    <i class="{{ $category->icon }}"></i>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Montant ({{ $currency }}) *</label>
                        <input type="number" step="0.01" name="amount" id="amount" 
                               class="form-control @error('amount') is-invalid @enderror" 
                               value="{{ old('amount', $transaction->amount) }}" placeholder="0.00" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Description de la transaction...">{{ old('description', $transaction->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="date" class="form-label">Date *</label>
                        <input type="date" name="date" id="date" 
                               class="form-control @error('date') is-invalid @enderror" 
                               value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
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

@push('scripts')
<script>
// Filter categories by type
function filterCategories() {
    const selectedType = document.querySelector('input[name="type"]:checked').value;
    const categorySelect = document.getElementById('category_id');
    const currentValue = categorySelect.value;
    
    Array.from(categorySelect.options).forEach(option => {
        if(option.value === '') return;
        const categoryType = option.getAttribute('data-type');
        option.style.display = categoryType === selectedType ? 'block' : 'none';
    });
    
    // Check if current value is still valid
    const selectedOption = Array.from(categorySelect.options).find(opt => opt.value == currentValue);
    if(!selectedOption || selectedOption.style.display === 'none') {
        categorySelect.value = '';
    }
}

document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', filterCategories);
});

filterCategories();
</script>
@endpush
@endsection