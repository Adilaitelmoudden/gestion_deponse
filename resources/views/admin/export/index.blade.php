@extends('layouts.app')

@section('title', 'Export CSV')
@section('header', 'Export CSV')

@section('content')
<div class="row g-4">
    {{-- Export Users --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h5><i class="fas fa-users me-2 text-primary"></i>Exporter les Utilisateurs</h5></div>
            <div class="card-body">
                <p style="color:var(--color-text-muted)">
                    Exporte tous les utilisateurs avec leurs statistiques financières :
                    ID, nom, email, rôle, statut, nombre de transactions, revenus, dépenses, solde, date d'inscription.
                </p>
                <div class="d-flex align-items-center gap-2 p-3 rounded mb-3"
                     style="background:var(--color-surface-hover);border:1px solid var(--color-border)">
                    <i class="fas fa-file-csv fa-2x text-success"></i>
                    <div>
                        <div class="fw-semibold" style="font-size:.9rem">users_YYYY-MM-DD.csv</div>
                        <small style="color:var(--color-text-muted)">Format UTF-8 BOM (compatible Excel)</small>
                    </div>
                </div>
                <a href="{{ route('admin.export.users') }}" class="btn btn-success w-100">
                    <i class="fas fa-download me-2"></i>Télécharger CSV Utilisateurs
                </a>
            </div>
        </div>
    </div>

    {{-- Export Transactions --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h5><i class="fas fa-exchange-alt me-2 text-info"></i>Exporter les Transactions</h5></div>
            <div class="card-body">
                <p style="color:var(--color-text-muted)">
                    Exporte les transactions avec filtres optionnels :
                    utilisateur, catégorie, type, montant, description, date.
                </p>

                <form action="{{ route('admin.export.transactions') }}" method="GET">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Date début</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Date fin</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="">Tous</option>
                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Revenus</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Dépenses</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Utilisateur</label>
                            <select name="user_id" class="form-select">
                                <option value="">Tous</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info w-100 text-white">
                        <i class="fas fa-download me-2"></i>Télécharger CSV Transactions
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
