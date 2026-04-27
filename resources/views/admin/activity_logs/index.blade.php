@extends('layouts.app')

@section('title', 'Journaux d\'activité')
@section('header', 'Journaux d\'activité')

@section('content')

{{-- ── Stats rapides ──────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-stats h-100" style="border-top:4px solid #4f46e5">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-list fa-2x" style="color:#4f46e5"></i></div>
                <div class="fs-2 fw-bold" style="color:var(--color-text-primary)">{{ number_format($statsToday['total']) }}</div>
                <div class="small" style="color:var(--color-text-muted)">Actions aujourd'hui</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-stats h-100" style="border-top:4px solid #16a34a">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-sign-in-alt fa-2x" style="color:#16a34a"></i></div>
                <div class="fs-2 fw-bold" style="color:var(--color-text-primary)">{{ $statsToday['logins'] }}</div>
                <div class="small" style="color:var(--color-text-muted)">Connexions</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-stats h-100" style="border-top:4px solid #0891b2">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-plus-circle fa-2x" style="color:#0891b2"></i></div>
                <div class="fs-2 fw-bold" style="color:var(--color-text-primary)">{{ $statsToday['created'] }}</div>
                <div class="small" style="color:var(--color-text-muted)">Créations</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-stats h-100" style="border-top:4px solid #dc2626">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-trash fa-2x" style="color:#dc2626"></i></div>
                <div class="fs-2 fw-bold" style="color:var(--color-text-primary)">{{ $statsToday['deleted'] }}</div>
                <div class="small" style="color:var(--color-text-muted)">Suppressions</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Filtres ─────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filtres</h5>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-times me-1"></i>Réinitialiser
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Recherche (description, IP...)"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="module" class="form-select form-select-sm">
                    <option value="">Tous les modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" @selected(request('module') === $mod)>
                            {{ ucfirst($mod) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Tous les utilisateurs</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ request('date_from') }}" placeholder="De">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ request('date_to') }}" placeholder="À">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Table des logs ──────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0">
            <i class="fas fa-history me-2 text-secondary"></i>
            Journal d'activité
            <span class="badge bg-secondary ms-1">{{ $logs->total() }}</span>
        </h5>

        {{-- Purge --}}
        <form method="POST" action="{{ route('admin.activity-logs.purge') }}"
              onsubmit="return confirm('Supprimer les logs anciens ?')">
            @csrf @method('DELETE')
            <div class="input-group input-group-sm">
                <span class="input-group-text">Purger >
                </span>
                <select name="days" class="form-select form-select-sm" style="max-width:100px">
                    <option value="30">30 jours</option>
                    <option value="60">60 jours</option>
                    <option value="90">90 jours</option>
                    <option value="180">6 mois</option>
                    <option value="365">1 an</option>
                </select>
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        @if(session('success'))
            <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.85rem">
                <thead class="table-light">
                    <tr>
                        <th style="width:160px">Date</th>
                        <th style="width:140px">Utilisateur</th>
                        <th style="width:110px">Module</th>
                        <th>Description</th>
                        <th style="width:120px">IP</th>
                        <th style="width:80px">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-muted small">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td>
                            @if($log->user)
                                <div class="fw-semibold">{{ $log->user->name }}</div>
                                <div class="text-muted small">{{ $log->user->email }}</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $log->module_label }}
                            </span>
                        </td>
                        <td>
                            <i class="fas {{ $log->action_icon }} text-{{ $log->action_color }} me-1"></i>
                            {{ $log->description }}
                            @if($log->meta)
                                <button class="btn btn-link btn-sm p-0 ms-1 text-muted"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#meta-{{ $log->id }}">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <div class="collapse mt-1" id="meta-{{ $log->id }}">
                                    <pre class="bg-light rounded p-2 small mb-0" style="font-size:.75rem">{{ json_encode($log->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            @endif
                        </td>
                        <td class="text-muted small font-monospace">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $log->action_color }}">
                                {{ Str::afterLast($log->action, '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                            Aucune activité enregistrée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($logs->hasPages())
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection
