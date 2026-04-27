@extends('layouts.app')

@section('title', 'Statistiques globales avancées')
@section('header', 'Statistiques globales avancées')

@section('content')

{{-- ── KPIs ce mois vs mois dernier ──────────────────────────────── --}}
<div class="row g-3 mb-4">
    @php
        $kpiCards = [
            ['label' => 'Transactions ce mois',   'value' => number_format($kpis['txThisMonth']),
             'growth' => $kpis['txGrowth'],   'icon' => 'fa-exchange-alt', 'color' => '#4f46e5'],
            ['label' => 'Revenus ce mois',        'value' => number_format($kpis['revThisMonth'],2) . ' ' . ($currency ?? 'MAD'),
             'growth' => $kpis['revGrowth'],  'icon' => 'fa-arrow-up',     'color' => '#16a34a'],
            ['label' => 'Dépenses ce mois',       'value' => number_format($kpis['expThisMonth'],2) . ' ' . ($currency ?? 'MAD'),
             'growth' => $kpis['expGrowth'],  'icon' => 'fa-arrow-down',   'color' => '#dc2626'],
            ['label' => 'Nouveaux utilisateurs',  'value' => number_format($kpis['usersThisMonth']),
             'growth' => $kpis['userGrowth'], 'icon' => 'fa-user-plus',    'color' => '#d97706'],
        ];
    @endphp

    @foreach($kpiCards as $card)
    <div class="col-6 col-md-3">
        <div class="card card-stats h-100" style="border-top:4px solid {{ $card['color'] }}">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="fs-4 fw-bold" style="color:var(--color-text-primary)">{{ $card['value'] }}</div>
                        <div class="small" style="color:var(--color-text-muted)">{{ $card['label'] }}</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:40px;height:40px;background:{{ $card['color'] }}22">
                        <i class="fas {{ $card['icon'] }}" style="color:{{ $card['color'] }}"></i>
                    </div>
                </div>
                <div class="mt-2">
                    @if($card['growth'] > 0)
                        <span class="badge bg-success"><i class="fas fa-caret-up me-1"></i>{{ $card['growth'] }}%</span>
                    @elseif($card['growth'] < 0)
                        <span class="badge bg-danger"><i class="fas fa-caret-down me-1"></i>{{ abs($card['growth']) }}%</span>
                    @else
                        <span class="badge bg-secondary">= 0%</span>
                    @endif
                    <span class="small text-muted ms-1">vs mois dernier</span>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Métriques comportementales ─────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card h-100 text-center">
            <div class="card-body py-3">
                <div class="fw-bold fs-4 text-primary">{{ $activityRate }}%</div>
                <div class="small text-muted">Taux d'activité</div>
                <div class="small text-muted">ce mois</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card h-100 text-center">
            <div class="card-body py-3">
                <div class="fw-bold fs-4 text-info">{{ $avgTxPerUser }}</div>
                <div class="small text-muted">Tx moy.</div>
                <div class="small text-muted">par utilisateur</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card h-100 text-center">
            <div class="card-body py-3">
                <div class="fw-bold fs-4 text-warning">{{ number_format($avgTxAmount,2) }}</div>
                <div class="small text-muted">Montant moy.</div>
                <div class="small text-muted">par transaction</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card h-100 text-center">
            <div class="card-body py-3">
                <div class="fw-bold fs-5 text-success">{{ $busyDay }}</div>
                <div class="small text-muted">Jour le plus</div>
                <div class="small text-muted">actif</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card h-100 text-center">
            <div class="card-body py-3">
                <div class="fw-bold fs-6 text-secondary">{{ $peakHour }}</div>
                <div class="small text-muted">Heure de</div>
                <div class="small text-muted">pointe</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card h-100 text-center">
            <div class="card-body py-3">
                <div class="fw-bold fs-4" style="color:{{ $budgetStats['rate'] > 50 ? '#dc2626' : '#16a34a' }}">
                    {{ $budgetStats['rate'] }}%
                </div>
                <div class="small text-muted">Budgets</div>
                <div class="small text-muted">dépassés</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Charts principaux ───────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    {{-- Revenus / Dépenses / Net 12 mois --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Revenus · Dépenses · Solde net (12 mois)</h5>
            </div>
            <div class="card-body"><canvas id="monthlyChart" height="110"></canvas></div>
        </div>
    </div>

    {{-- Donut types --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-warning"></i>Répartition des transactions</h5>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <canvas id="typeDonut" height="200"></canvas>
                <div class="mt-3 d-flex gap-4">
                    <div class="text-center">
                        <div class="fw-bold text-success">{{ number_format($typeDonut['income']) }}</div>
                        <div class="small text-muted">Revenus</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-danger">{{ number_format($typeDonut['expense']) }}</div>
                        <div class="small text-muted">Dépenses</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Croissance utilisateurs --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users me-2 text-success"></i>Croissance utilisateurs (12 mois)</h5>
            </div>
            <div class="card-body"><canvas id="userGrowthChart" height="160"></canvas></div>
        </div>
    </div>

    {{-- Top catégories --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tags me-2 text-info"></i>Top 10 catégories (montant total)</h5>
            </div>
            <div class="card-body"><canvas id="categoriesChart" height="160"></canvas></div>
        </div>
    </div>
</div>

{{-- ── Heatmap activité 30 jours ───────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-secondary"></i>Activité des 30 derniers jours (transactions)</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-1" id="heatmap">
            @php
                $maxCount = collect($activityHeatmap)->max('count') ?: 1;
            @endphp
            @foreach($activityHeatmap as $day)
                @php
                    $opacity = $day['count'] > 0 ? max(0.15, $day['count'] / $maxCount) : 0;
                    $bg = $day['count'] > 0 ? "rgba(79,70,229,{$opacity})" : 'var(--color-border)';
                @endphp
                <div title="{{ $day['date'] }} : {{ $day['count'] }} transactions"
                     style="width:28px;height:28px;border-radius:4px;background:{{ $bg }};cursor:default"
                     class="d-flex align-items-center justify-content-center">
                    <span class="small" style="font-size:.6rem;color:{{ $day['count'] > $maxCount/2 ? 'white' : 'transparent' }}">
                        {{ $day['count'] }}
                    </span>
                </div>
            @endforeach
        </div>
        <div class="mt-2 d-flex align-items-center gap-2 text-muted small">
            <span>Moins</span>
            @foreach([0.05, 0.25, 0.5, 0.75, 1] as $o)
            <div style="width:16px;height:16px;border-radius:3px;background:rgba(79,70,229,{{ $o }})"></div>
            @endforeach
            <span>Plus</span>
        </div>
    </div>
</div>

{{-- ── Tableaux inférieurs ─────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    {{-- Top spenders --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-fire me-2 text-danger"></i>Top 5 plus grosses dépenses (utilisateurs)</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Utilisateur</th><th class="text-end">Dépensé</th><th class="text-end">Tx</th></tr>
                    </thead>
                    <tbody>
                        @forelse($topSpenders as $i => $u)
                        <tr>
                            <td><span class="badge bg-danger">{{ $i+1 }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $u->name }}</div>
                                <div class="text-muted small">{{ $u->email }}</div>
                            </td>
                            <td class="text-end fw-bold text-danger">{{ number_format($u->total_spent,2) }}</td>
                            <td class="text-end text-muted">{{ $u->tx_count }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Aucune donnée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Budgets & Savings --}}
    <div class="col-md-6">
        <div class="row g-3 h-100">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fas fa-wallet me-2 text-warning"></i>Budgets</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="fs-4 fw-bold">{{ $budgetStats['total'] }}</div>
                                <div class="small text-muted">Total</div>
                            </div>
                            <div class="col-4">
                                <div class="fs-4 fw-bold text-danger">{{ $budgetStats['exceeded'] }}</div>
                                <div class="small text-muted">Dépassés</div>
                            </div>
                            <div class="col-4">
                                <div class="fs-4 fw-bold" style="color:{{ $budgetStats['rate'] > 50 ? '#dc2626' : '#16a34a' }}">
                                    {{ $budgetStats['rate'] }}%
                                </div>
                                <div class="small text-muted">Taux dép.</div>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height:8px">
                            <div class="progress-bar bg-danger" style="width:{{ $budgetStats['rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fas fa-piggy-bank me-2 text-success"></i>Objectifs d'épargne</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="fs-4 fw-bold">{{ $savingsStats['total'] }}</div>
                                <div class="small text-muted">Total</div>
                            </div>
                            <div class="col-4">
                                <div class="fs-4 fw-bold text-success">{{ $savingsStats['completed'] }}</div>
                                <div class="small text-muted">Atteints</div>
                            </div>
                            <div class="col-4">
                                <div class="fs-4 fw-bold text-success">{{ $savingsStats['rate'] }}%</div>
                                <div class="small text-muted">Taux</div>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height:8px">
                            <div class="progress-bar bg-success" style="width:{{ $savingsStats['rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Logs par module (7 jours) ───────────────────────────────────── --}}
@if($logsByModule->isNotEmpty())
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-history me-2 text-secondary"></i>Activité par module — 7 derniers jours</h5>
    </div>
    <div class="card-body">
        @php $maxLog = $logsByModule->max('count') ?: 1; @endphp
        @foreach($logsByModule as $log)
        <div class="mb-2">
            <div class="d-flex justify-content-between small mb-1">
                <span>{{ ucfirst($log->module) }}</span>
                <strong>{{ $log->count }}</strong>
            </div>
            <div class="progress" style="height:6px">
                <div class="progress-bar bg-primary" style="width:{{ ($log->count / $maxLog) * 100 }}%"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
const textColor = isDark ? '#cbd5e1' : '#64748b';

Chart.defaults.color = textColor;
Chart.defaults.borderColor = gridColor;

// ── 1. Revenus / Dépenses / Net ──────────────────────────────────
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: @json($monthlyChart['labels']),
        datasets: [
            {
                label: 'Revenus',
                data: @json($monthlyChart['income']),
                backgroundColor: 'rgba(22,163,74,0.7)',
                borderColor: '#16a34a',
                borderWidth: 1,
                borderRadius: 4,
            },
            {
                label: 'Dépenses',
                data: @json($monthlyChart['expense']),
                backgroundColor: 'rgba(220,38,38,0.7)',
                borderColor: '#dc2626',
                borderWidth: 1,
                borderRadius: 4,
            },
            {
                label: 'Solde net',
                data: @json($monthlyChart['net']),
                type: 'line',
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79,70,229,0.1)',
                borderWidth: 2,
                pointRadius: 3,
                fill: true,
                tension: 0.4,
                yAxisID: 'y',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'top' } },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: gridColor }, ticks: { color: textColor } }
        }
    }
});

// ── 2. Donut type transactions ───────────────────────────────────
new Chart(document.getElementById('typeDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Revenus', 'Dépenses'],
        datasets: [{
            data: [{{ $typeDonut['income'] }}, {{ $typeDonut['expense'] }}],
            backgroundColor: ['rgba(22,163,74,0.8)', 'rgba(220,38,38,0.8)'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { position: 'bottom' } }
    }
});

// ── 3. Croissance utilisateurs ───────────────────────────────────
new Chart(document.getElementById('userGrowthChart'), {
    type: 'line',
    data: {
        labels: @json($userGrowthChart['labels']),
        datasets: [
            {
                label: 'Nouveaux',
                data: @json($userGrowthChart['counts']),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79,70,229,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
            },
            {
                label: 'Cumulatif',
                data: @json($userGrowthChart['cumulative']),
                borderColor: '#d97706',
                backgroundColor: 'transparent',
                borderDash: [5,5],
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 0,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: gridColor }, ticks: { color: textColor } }
        }
    }
});

// ── 4. Top catégories (horizontal bar) ──────────────────────────
new Chart(document.getElementById('categoriesChart'), {
    type: 'bar',
    data: {
        labels: @json($topCategories->pluck('name')),
        datasets: [{
            label: 'Montant total',
            data: @json($topCategories->pluck('total_amount')),
            backgroundColor: @json($topCategories->map(fn($c) => $c->color ?? '#4f46e5')),
            borderRadius: 4,
            borderWidth: 0,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor } },
            y: { grid: { display: false } }
        }
    }
});
</script>
@endpush
