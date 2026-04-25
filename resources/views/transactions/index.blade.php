@extends('layouts.app')

@section('title', 'Transactions')
@section('header', 'Gestion des Transactions')

@section('content')

{{-- ── Filtres ──────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtres</h5>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filterPanel">
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" id="filterForm" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control" onchange="this.form.submit()">
                        <option value="all">Tous</option>
                        <option value="income"  {{ request('type') == 'income'  ? 'selected' : '' }}>Revenus</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Dépenses</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Catégorie</label>
                    <select name="category_id" class="form-control" onchange="this.form.submit()">
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

                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Description ou montant…" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>

                {{-- NEW: Amount range --}}
                <div class="col-md-2">
                    <label class="form-label">Montant min (DH)</label>
                    <input type="number" name="min_amount" step="0.01" class="form-control" placeholder="0" value="{{ request('min_amount') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Montant max (DH)</label>
                    <input type="number" name="max_amount" step="0.01" class="form-control" placeholder="9999" value="{{ request('max_amount') }}">
                </div>

                {{-- Hidden sort fields --}}
                <input type="hidden" name="sort"     value="{{ request('sort', 'date') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Résumé ───────────────────────────────────────────── --}}
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card card-stats bg-success text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="small opacity-75">Total Revenus (all time)</div>
                    <div class="fs-4 fw-bold">{{ number_format($totalIncome, 2) }} DH</div>
                </div>
                <i class="fas fa-arrow-up fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card card-stats bg-danger text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="small opacity-75">Total Dépenses (all time)</div>
                    <div class="fs-4 fw-bold">{{ number_format($totalExpense, 2) }} DH</div>
                </div>
                <i class="fas fa-arrow-down fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card card-stats {{ ($totalIncome - $totalExpense) >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="small opacity-75">Solde net</div>
                    <div class="fs-4 fw-bold">{{ number_format($totalIncome - $totalExpense, 2) }} DH</div>
                </div>
                <i class="fas fa-wallet fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

{{-- ── Liste ─────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Transactions
            <span class="badge bg-secondary ms-2">{{ $transactions->total() }}</span>
        </h5>
        <div class="d-flex gap-2 flex-wrap">
            {{-- NEW: CSV Export --}}
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm btn-outline-success">
                <i class="fas fa-file-csv me-1"></i>Exporter CSV
            </a>
            <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>Nouvelle Transaction
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll" title="Tout sélectionner">
                        </th>
                        {{-- NEW: sortable columns --}}
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'sort_dir' => request('sort') == 'date' && request('sort_dir','desc') == 'asc' ? 'desc' : 'asc']) }}" class="sort-link {{ request('sort','date') == 'date' ? 'sort-active' : '' }}">
                                Date
                                @if(request('sort','date') == 'date')
                                    <i class="fas fa-sort-{{ request('sort_dir','desc') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort text-muted ms-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>Description</th>
                        <th>Catégorie</th>
                        <th>Type</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'sort_dir' => request('sort') == 'amount' && request('sort_dir','desc') == 'asc' ? 'desc' : 'asc']) }}" class="sort-link {{ request('sort') == 'amount' ? 'sort-active' : '' }}">
                                Montant
                                @if(request('sort') == 'amount')
                                    <i class="fas fa-sort-{{ request('sort_dir','desc') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort text-muted ms-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr class="transaction-row" data-id="{{ $transaction->id }}">
                        <td>
                            <input type="checkbox" class="transaction-checkbox" value="{{ $transaction->id }}">
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $transaction->date->format('d/m/Y') }}</span>
                        </td>
                        <td class="text-truncate" style="max-width:200px" title="{{ $transaction->description }}">
                            {{ $transaction->description ?: '—' }}
                        </td>
                        <td>
                            <span class="badge-category" style="background-color: {{ $transaction->category->color ?? '#888' }}20; color: {{ $transaction->category->color ?? '#888' }}">
                                <i class="{{ $transaction->category->icon ?? 'fas fa-tag' }}"></i>
                                {{ $transaction->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $transaction->type == 'income' ? 'bg-success' : 'bg-danger' }}">
                                {{ $transaction->type == 'income' ? 'Revenu' : 'Dépense' }}
                            </span>
                        </td>
                        <td class="{{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ $transaction->type == 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} DH
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-outline-info" title="Détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-2">Aucune transaction trouvée</p>
                            <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Ajouter une transaction
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
        {{-- Bulk delete --}}
        <div id="bulkActions" style="display:none">
            <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn">
                <i class="fas fa-trash-alt me-1"></i>Supprimer sélectionnés (<span id="selectedCount">0</span>)
            </button>
        </div>
        <div id="noBulk">
            <small class="text-muted">Sélectionnez des lignes pour les actions groupées</small>
        </div>

        {{-- Pagination --}}
        <div>
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<form id="bulkDeleteForm" action="{{ route('transactions.bulk-delete') }}" method="POST" style="display:none">
    @csrf
    <input type="hidden" name="ids" id="bulkIds">
</form>

@push('styles')
<style>
.sort-link { color: var(--color-text-primary); text-decoration: none; white-space: nowrap; }
.sort-link:hover { color: var(--color-input-focus); }
.sort-active { color: var(--color-input-focus); font-weight: 600; }
.transaction-row.selected { background: color-mix(in srgb, var(--color-input-focus) 8%, transparent) !important; }
.badge-category { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 0.82rem; font-weight: 500; }
</style>
@endpush

@push('scripts')
<script>
// Select all
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.transaction-checkbox').forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('selected', this.checked);
    });
    updateBulkUI();
});

// Individual checkbox
document.querySelectorAll('.transaction-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('selected', this.checked);
        updateBulkUI();
    });
});

function updateBulkUI() {
    const count = document.querySelectorAll('.transaction-checkbox:checked').length;
    document.getElementById('bulkActions').style.display = count > 0 ? 'block' : 'none';
    document.getElementById('noBulk').style.display      = count > 0 ? 'none'  : 'block';
    document.getElementById('selectedCount').textContent = count;
}

// Bulk delete
document.getElementById('bulkDeleteBtn')?.addEventListener('click', function() {
    const ids = Array.from(document.querySelectorAll('.transaction-checkbox:checked')).map(cb => cb.value);
    if (confirm(`Supprimer ${ids.length} transaction(s) définitivement ?`)) {
        document.getElementById('bulkIds').value = JSON.stringify(ids);
        document.getElementById('bulkDeleteForm').submit();
    }
});

// Confirm single delete
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', e => {
        if (!confirm('Supprimer cette transaction ?')) e.preventDefault();
    });
});
</script>
@endpush
@endsection
