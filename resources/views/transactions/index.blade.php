@extends('layouts.app')

@section('title', 'Transactions')
@section('header', 'Gestion des Transactions')

@section('content')
<!-- Filtres -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-filter"></i> Filtres</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('transactions.index') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type" class="form-control">
                    <option value="all">Tous</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Revenus</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Dépenses</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Catégorie</label>
                <select name="category_id" class="form-control">
                    <option value="">Toutes</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Date début</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Date fin</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Recherche</label>
                <input type="text" name="search" class="form-control" placeholder="Description..." value="{{ request('search') }}">
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Résumé -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Total Revenus</h6>
                <h3>{{ number_format($totalIncome, 2) }} DH</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6>Total Dépenses</h6>
                <h3>{{ number_format($totalExpense, 2) }} DH</h3>
            </div>
        </div>
    </div>
</div>

<!-- Liste des transactions -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-list"></i> Liste des Transactions</h5>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Transaction
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Catégorie</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>
                            <input type="checkbox" class="transaction-checkbox" value="{{ $transaction->id }}">
                        </td>
                        <td>{{ $transaction->date->format('d/m/Y') }}</td>
                        <td>{{ $transaction->description ?: '-' }}</td>
                        <td>
                            <span class="badge-category" style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }}">
                                <i class="{{ $transaction->category->icon }}"></i>
                                {{ $transaction->category->name }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $transaction->type == 'income' ? 'bg-success' : 'bg-danger' }}">
                                {{ $transaction->type == 'income' ? 'Revenu' : 'Dépense' }}
                            </span>
                        </td>
                        <td class="{{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                            <strong>
                                {{ $transaction->type == 'income' ? '+' : '-' }}
                                {{ number_format($transaction->amount, 2) }} DH
                            </strong>
                        </td>
                        <td>
                            <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-info btn-action">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-warning btn-action">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Supprimer cette transaction ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            Aucune transaction trouvée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Bulk delete -->
        <div class="row mt-3" id="bulkActions" style="display: none;">
            <div class="col-12">
                <button type="button" class="btn btn-danger" id="bulkDeleteBtn">
                    <i class="fas fa-trash-alt"></i> Supprimer sélectionnés
                </button>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<form id="bulkDeleteForm" action="{{ route('transactions.bulk-delete') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkIds">
</form>

@push('scripts')
<script>
// Select all checkboxes
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.transaction-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    toggleBulkActions();
});

// Toggle bulk actions
document.querySelectorAll('.transaction-checkbox').forEach(cb => {
    cb.addEventListener('change', toggleBulkActions);
});

function toggleBulkActions() {
    const checked = document.querySelectorAll('.transaction-checkbox:checked').length;
    document.getElementById('bulkActions').style.display = checked > 0 ? 'block' : 'none';
}

// Bulk delete
document.getElementById('bulkDeleteBtn')?.addEventListener('click', function() {
    const checked = document.querySelectorAll('.transaction-checkbox:checked');
    const ids = Array.from(checked).map(cb => cb.value);
    
    if(confirm(`Supprimer ${ids.length} transaction(s) ?`)) {
        document.getElementById('bulkIds').value = JSON.stringify(ids);
        document.getElementById('bulkDeleteForm').submit();
    }
});
</script>
@endpush
@endsection