@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header', 'Admin Dashboard')

@section('content')
<div class="row g-3 mb-4">
    {{-- Stat cards --}}
    <div class="col-6 col-md-3">
        <div class="card card-stats h-100" style="border-top:4px solid #4f46e5">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-users fa-2x" style="color:#4f46e5"></i></div>
                <div class="fs-2 fw-bold" style="color:var(--color-text-primary)">{{ $totalUsers }}</div>
                <div class="small" style="color:var(--color-text-muted)">Utilisateurs</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-stats h-100" style="border-top:4px solid #16a34a">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-user-check fa-2x" style="color:#16a34a"></i></div>
                <div class="fs-2 fw-bold" style="color:var(--color-text-primary)">{{ $activeUsers }}</div>
                <div class="small" style="color:var(--color-text-muted)">Actifs</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-stats h-100" style="border-top:4px solid #dc2626">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-user-times fa-2x" style="color:#dc2626"></i></div>
                <div class="fs-2 fw-bold" style="color:var(--color-text-primary)">{{ $inactiveUsers }}</div>
                <div class="small" style="color:var(--color-text-muted)">Inactifs</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-stats h-100" style="border-top:4px solid #d97706">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-user-plus fa-2x" style="color:#d97706"></i></div>
                <div class="fs-2 fw-bold" style="color:var(--color-text-primary)">{{ $newThisMonth }}</div>
                <div class="small" style="color:var(--color-text-muted)">Nouveaux ce mois</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4">
        <div class="card card-stats h-100" style="border-top:4px solid #0891b2">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-exchange-alt fa-2x" style="color:#0891b2"></i></div>
                <div class="fs-2 fw-bold" style="color:var(--color-text-primary)">{{ number_format($totalTransactions) }}</div>
                <div class="small" style="color:var(--color-text-muted)">Transactions totales</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card card-stats h-100" style="border-top:4px solid #16a34a">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-arrow-up fa-2x" style="color:#16a34a"></i></div>
                <div class="fs-2 fw-bold" style="color:#16a34a">{{ number_format($totalIncome, 2) }} {{ $currency }}</div>
                <div class="small" style="color:var(--color-text-muted)">Revenus totaux</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card card-stats h-100" style="border-top:4px solid #dc2626">
            <div class="card-body text-center">
                <div class="mb-1"><i class="fas fa-arrow-down fa-2x" style="color:#dc2626"></i></div>
                <div class="fs-2 fw-bold" style="color:#dc2626">{{ number_format($totalExpense, 2) }} {{ $currency }}</div>
                <div class="small" style="color:var(--color-text-muted)">Dépenses totales</div>
            </div>
        </div>
    </div>
</div>

{{-- Charts row --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header"><h5><i class="fas fa-chart-bar me-2 text-primary"></i>Revenus vs Dépenses (12 derniers mois)</h5></div>
            <div class="card-body"><canvas id="revenueExpenseChart" height="100"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><h5><i class="fas fa-chart-line me-2 text-success"></i>Nouvelles inscriptions (6 mois)</h5></div>
            <div class="card-body"><canvas id="registrationsChart" height="200"></canvas></div>
        </div>
    </div>
</div>

{{-- Tables row --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-trophy me-2 text-warning"></i>Top 5 Utilisateurs Actifs</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr>
                            <th>#</th><th>Nom</th><th>Email</th><th class="text-end">Transactions</th>
                        </tr></thead>
                        <tbody>
                        @forelse($topUsers as $i => $u)
                            <tr>
                                <td><span class="badge" style="background:#4f46e5">{{ $i+1 }}</span></td>
                                <td>
                                    <a href="{{ route('admin.users.profile', $u) }}" class="text-decoration-none fw-semibold">{{ $u->name }}</a>
                                </td>
                                <td style="color:var(--color-text-muted)">{{ $u->email }}</td>
                                <td class="text-end"><span class="badge bg-secondary">{{ $u->transactions_count }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center" style="color:var(--color-text-muted)">Aucune donnée</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-tags me-2 text-danger"></i>Top 5 Catégories de Dépenses</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr>
                            <th>Catégorie</th><th class="text-end">Total dépensé</th>
                        </tr></thead>
                        <tbody>
                        @forelse($topCategories as $cat)
                            <tr>
                                <td>{{ $cat->name }}</td>
                                <td class="text-end text-danger fw-semibold">{{ number_format($cat->total_spent, 2) }} {{ $currency }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center" style="color:var(--color-text-muted)">Aucune donnée</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2 text-info"></i>Derniers Inscrits</h5>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Nom</th><th>Rôle</th><th>Statut</th><th>Inscrit</th></tr></thead>
                        <tbody>
                        @foreach($recentUsers as $u)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.profile', $u) }}" class="text-decoration-none">{{ $u->name }}</a>
                                </td>
                                <td>
                                    @if($u->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @else
                                        <span class="badge bg-secondary">User</span>
                                    @endif
                                </td>
                                <td>
                                    @if($u->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Inactif</span>
                                    @endif
                                </td>
                                <td style="color:var(--color-text-muted);font-size:.8rem">{{ $u->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-history me-2 text-primary"></i>Transactions Récentes (tous utilisateurs)</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Utilisateur</th><th>Catégorie</th><th>Type</th><th class="text-end">Montant</th><th>Date</th></tr></thead>
                        <tbody>
                        @forelse($recentTransactions as $t)
                            <tr>
                                <td>{{ $t->user?->name ?? '-' }}</td>
                                <td>{{ $t->category?->name ?? '-' }}</td>
                                <td>
                                    @if($t->type === 'income')
                                        <span class="badge bg-success">Revenu</span>
                                    @else
                                        <span class="badge bg-danger">Dépense</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold {{ $t->type === 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $t->type === 'income' ? '+' : '-' }}{{ number_format($t->amount, 2) }} {{ $currency }}
                                </td>
                                <td style="color:var(--color-text-muted);font-size:.8rem">{{ $t->date?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center" style="color:var(--color-text-muted)">Aucune transaction</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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

    // Revenue vs Expense bar chart
    const ctx1 = document.getElementById('revenueExpenseChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: @json($revenueExpenseChart['labels']),
                datasets: [
                    {
                        label: 'Revenus',
                        data: @json($revenueExpenseChart['income']),
                        backgroundColor: 'rgba(22,163,74,0.75)',
                        borderRadius: 5,
                    },
                    {
                        label: 'Dépenses',
                        data: @json($revenueExpenseChart['expense']),
                        backgroundColor: 'rgba(220,38,38,0.75)',
                        borderRadius: 5,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: textColor } } },
                scales: {
                    x: { ticks: { color: textColor }, grid: { color: gridColor } },
                    y: { ticks: { color: textColor }, grid: { color: gridColor } }
                }
            }
        });
    }

    // Registrations line chart
    const ctx2 = document.getElementById('registrationsChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: @json($registrationsChart['labels']),
                datasets: [{
                    label: 'Inscriptions',
                    data: @json($registrationsChart['counts']),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.12)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#4f46e5',
                    pointRadius: 4,
                }]
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
