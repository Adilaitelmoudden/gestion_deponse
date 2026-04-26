@extends('layouts.app')

@section('title', 'Profil - ' . $user->name)
@section('header', 'Profil Utilisateur')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
    </a>
</div>

<div class="row g-3 mb-4">
    {{-- Personal info card --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><h5><i class="fas fa-id-card me-2 text-primary"></i>Informations personnelles</h5></div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:inline-flex;align-items:center;justify-content:center;margin-bottom:.75rem">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">{{ $user->name }}</h5>
                    <small style="color:var(--color-text-muted)">{{ $user->email }}</small>
                </div>
                <hr style="border-color:var(--color-border)">
                <table class="w-100" style="font-size:.9rem">
                    <tr>
                        <td style="color:var(--color-text-muted);padding:.35rem 0">ID</td>
                        <td class="text-end fw-semibold">{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--color-text-muted);padding:.35rem 0">Rôle</td>
                        <td class="text-end">
                            @if($user->role === 'admin')
                                <span class="badge bg-danger">Admin</span>
                            @else
                                <span class="badge bg-secondary">Utilisateur</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="color:var(--color-text-muted);padding:.35rem 0">Statut</td>
                        <td class="text-end">
                            @if($user->is_active)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-warning text-dark">Inactif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="color:var(--color-text-muted);padding:.35rem 0">Inscrit le</td>
                        <td class="text-end">{{ $user->created_at?->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--color-text-muted);padding:.35rem 0">Dernière connexion</td>
                        <td class="text-end">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</td>
                    </tr>
                </table>

                <div class="d-grid mt-3 gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                    <a href="{{ route('admin.users.toggle', $user) }}" class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} me-1"></i>
                        {{ $user->is_active ? 'Désactiver' : 'Activer' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial stats --}}
    <div class="col-md-8">
        <div class="row g-3 mb-3">
            <div class="col-4">
                <div class="card card-stats text-center" style="border-top:4px solid #16a34a">
                    <div class="card-body">
                        <div class="small mb-1" style="color:var(--color-text-muted)">Revenus totaux</div>
                        <div class="fw-bold fs-5 text-success">{{ number_format($totalIncome, 2) }} DH</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card card-stats text-center" style="border-top:4px solid #dc2626">
                    <div class="card-body">
                        <div class="small mb-1" style="color:var(--color-text-muted)">Dépenses totales</div>
                        <div class="fw-bold fs-5 text-danger">{{ number_format($totalExpense, 2) }} DH</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card card-stats text-center" style="border-top:4px solid #4f46e5">
                    <div class="card-body">
                        <div class="small mb-1" style="color:var(--color-text-muted)">Solde</div>
                        <div class="fw-bold fs-5 {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($balance, 2) }} DH
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6-month chart --}}
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-chart-area me-2 text-info"></i>Activité financière (6 derniers mois)</h5></div>
            <div class="card-body"><canvas id="userFinanceChart" height="110"></canvas></div>
        </div>
    </div>
</div>

{{-- Expenses by category --}}
@if($expenseByCategory->count())
<div class="card mb-4">
    <div class="card-header"><h5><i class="fas fa-chart-pie me-2 text-danger"></i>Dépenses par catégorie</h5></div>
    <div class="card-body">
        @php $maxSpent = $expenseByCategory->max('total'); @endphp
        @foreach($expenseByCategory as $item)
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span style="font-size:.88rem;font-weight:600">{{ $item->category?->name ?? 'Sans catégorie' }}</span>
                    <span style="font-size:.85rem;color:var(--color-text-muted)">{{ number_format($item->total, 2) }} DH</span>
                </div>
                <div class="progress" style="height:8px">
                    <div class="progress-bar bg-danger" style="width:{{ $maxSpent > 0 ? ($item->total / $maxSpent * 100) : 0 }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Last 10 transactions --}}
<div class="card">
    <div class="card-header"><h5><i class="fas fa-history me-2 text-primary"></i>10 Dernières Transactions</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>Catégorie</th><th>Description</th><th>Type</th>
                    <th class="text-end">Montant</th><th>Date</th>
                </tr></thead>
                <tbody>
                @forelse($recentTransactions as $t)
                    <tr>
                        <td>{{ $t->category?->name ?? '-' }}</td>
                        <td style="color:var(--color-text-muted)">{{ Str::limit($t->description ?? '-', 40) }}</td>
                        <td>
                            @if($t->type === 'income')
                                <span class="badge bg-success">Revenu</span>
                            @else
                                <span class="badge bg-danger">Dépense</span>
                            @endif
                        </td>
                        <td class="text-end fw-semibold {{ $t->type === 'income' ? 'text-success' : 'text-danger' }}">
                            {{ $t->type === 'income' ? '+' : '-' }}{{ number_format($t->amount, 2) }} DH
                        </td>
                        <td style="color:var(--color-text-muted);font-size:.82rem">{{ $t->date?->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-3" style="color:var(--color-text-muted)">Aucune transaction</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    const ctx = document.getElementById('userFinanceChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Revenus',
                        data: @json($chartIncome),
                        backgroundColor: 'rgba(22,163,74,0.7)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Dépenses',
                        data: @json($chartExpense),
                        backgroundColor: 'rgba(220,38,38,0.7)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: textColor } } },
                scales: {
                    x: { ticks: { color: textColor }, grid: { color: gridColor } },
                    y: { ticks: { color: textColor }, grid: { color: gridColor }, beginAtZero: true }
                }
            }
        });
    }
});
</script>
@endpush
