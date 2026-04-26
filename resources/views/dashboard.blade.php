@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Tableau de Bord')

@section('content')

{{-- ══════════════════════════════════════════════════════════
     NOUVEAU — Barre d'actions rapides
══════════════════════════════════════════════════════════ --}}
<div class="d-flex gap-2 mb-4 flex-wrap quick-actions-bar">
    <a href="{{ route('transactions.create') }}" class="btn btn-success btn-sm quick-action-btn">
        <i class="fas fa-plus me-1"></i> Revenu
    </a>
    <a href="{{ route('transactions.create') }}" class="btn btn-danger btn-sm quick-action-btn">
        <i class="fas fa-minus me-1"></i> Dépense
    </a>
    <a href="{{ route('budgets.create') }}" class="btn btn-warning btn-sm quick-action-btn">
        <i class="fas fa-bullseye me-1"></i> Budget
    </a>
    <a href="{{ route('transactions.index') }}?export=csv" class="btn btn-outline-secondary btn-sm quick-action-btn">
        <i class="fas fa-download me-1"></i> Exporter CSV
    </a>
    <a href="{{ route('savings_goals.index') }}" class="btn btn-outline-info btn-sm quick-action-btn">
        <i class="fas fa-piggy-bank me-1"></i> Objectifs
    </a>
</div>

{{-- ══════════════════════════════════════════════════════════
     NOUVEAU — Insights intelligents
══════════════════════════════════════════════════════════ --}}
@if(!empty($insights))
<div class="row mb-4 g-2">
    @foreach($insights as $insight)
    <div class="col-md-{{ count($insights) == 1 ? 12 : (count($insights) == 2 ? 6 : 4) }}">
        <div class="alert alert-{{ $insight['type'] }} mb-0 d-flex align-items-center gap-2 insight-card py-2 px-3">
            <i class="fas {{ $insight['icon'] }} fa-sm flex-shrink-0"></i>
            <span class="small">{{ $insight['text'] }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ── Stats Cards ──────────────────────────────────────── --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card card-stats h-100" style="border-top: 3px solid #22c55e">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Revenus ce mois</div>
                        <div class="fs-3 fw-bold text-success">
                            <span class="count-up" data-target="{{ $totalIncome }}">{{ number_format($totalIncome, 2) }}</span> DH
                        </div>
                        {{-- NOUVEAU sparkline --}}
                        <canvas class="sparkline mt-2" data-values="{{ json_encode(array_column($last7Days, 'income')) }}" data-color="#22c55e" height="36" style="width:100%;max-height:36px"></canvas>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:#22c55e20">
                        <i class="fas fa-arrow-up text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats h-100" style="border-top: 3px solid #ef4444">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Dépenses ce mois</div>
                        <div class="fs-3 fw-bold text-danger">
                            <span class="count-up" data-target="{{ $totalExpense }}">{{ number_format($totalExpense, 2) }}</span> DH
                        </div>
                        <canvas class="sparkline mt-2" data-values="{{ json_encode(array_column($last7Days, 'expense')) }}" data-color="#ef4444" height="36" style="width:100%;max-height:36px"></canvas>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:#ef444420">
                        <i class="fas fa-arrow-down text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats h-100" style="border-top: 3px solid {{ $balance >= 0 ? '#4f46e5' : '#f59e0b' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Solde</div>
                        <div class="fs-3 fw-bold {{ $balance >= 0 ? 'text-primary' : 'text-danger' }}">
                            <span class="count-up" data-target="{{ $balance }}">{{ number_format($balance, 2) }}</span> DH
                        </div>
                        <div class="small mt-1">
                            @if($balance >= 0)
                                <i class="fas fa-check-circle text-success me-1"></i><span class="text-success">Positif</span>
                            @else
                                <span class="badge bg-danger py-1 px-2"><i class="fas fa-exclamation-triangle me-1"></i>Déficit !</span>
                            @endif
                        </div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:{{ $balance >= 0 ? '#4f46e520' : '#ef444420' }}">
                        <i class="fas fa-wallet" style="color:{{ $balance >= 0 ? '#4f46e5' : '#ef4444' }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Taux d'épargne --}}
    <div class="col-md-3 mb-3">
        @php $savingsRate = $totalIncome > 0 ? round(($balance / $totalIncome) * 100, 1) : 0; @endphp
        <div class="card card-stats h-100" style="border-top: 3px solid #06b6d4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Taux d'épargne</div>
                        <div class="fs-3 fw-bold" style="color:#06b6d4">{{ $savingsRate }}%</div>
                        <div class="small text-muted">du revenu mensuel</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:#06b6d420">
                        <i class="fas fa-piggy-bank" style="color:#06b6d4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     NOUVEAU — Score de santé financière + Factures à venir
══════════════════════════════════════════════════════════ --}}
<div class="row mb-4">
    {{-- Score de santé --}}
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-heart-pulse me-2 text-danger"></i>Santé financière</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                @php
                    $hColor = $healthScore >= 70 ? '#22c55e' : ($healthScore >= 40 ? '#f59e0b' : '#ef4444');
                    $hLabel = $healthScore >= 70 ? 'Excellente' : ($healthScore >= 40 ? 'Correcte' : 'À améliorer');
                    $circumference = 2 * pi() * 40;
                    $dash = ($healthScore / 100) * $circumference;
                @endphp
                <svg width="110" height="110" viewBox="0 0 100 100" class="health-gauge">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#e2e8f0" stroke-width="9"/>
                    <circle cx="50" cy="50" r="40" fill="none"
                        stroke="{{ $hColor }}"
                        stroke-width="9"
                        stroke-dasharray="{{ $dash }} {{ $circumference }}"
                        stroke-dashoffset="0"
                        stroke-linecap="round"
                        transform="rotate(-90 50 50)"
                        class="gauge-arc"/>
                    <text x="50" y="46" text-anchor="middle" font-size="20" font-weight="bold" fill="{{ $hColor }}">{{ $healthScore }}</text>
                    <text x="50" y="60" text-anchor="middle" font-size="9" fill="#94a3b8">/100</text>
                </svg>
                <div class="mt-2 fw-semibold" style="color:{{ $hColor }}">{{ $hLabel }}</div>
                <div class="small text-muted text-center mt-1">Basé sur épargne, budgets<br>et objectifs actifs</div>
            </div>
        </div>
    </div>

    {{-- Factures à venir --}}
    <div class="col-md-8 mb-3">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2 text-info"></i>Factures à venir — 7 jours</h6>
                @if($upcomingBillsTotal > 0)
                <span class="badge bg-info text-white">{{ number_format($upcomingBillsTotal, 2) }} DH</span>
                @endif
            </div>
            <div class="card-body p-0">
                @forelse($upcomingBills as $bill)
                <div class="d-flex align-items-center px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }} upcoming-bill-row">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                         style="width:34px;height:34px;background:{{ ($bill->category->color ?? '#888') }}20">
                        <i class="{{ $bill->category->icon ?? 'fas fa-repeat' }} fa-sm" style="color:{{ $bill->category->color ?? '#888' }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $bill->name }}</div>
                        <div class="text-muted" style="font-size:11px">{{ \Carbon\Carbon::parse($bill->next_due_date)->format('d/m/Y') }}</div>
                    </div>
                    <div class="text-danger fw-bold small">{{ number_format($bill->amount, 2) }} DH</div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-calendar-check fa-2x mb-2 d-block text-success"></i>
                    <span class="small">Aucune facture dans les 7 prochains jours</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ── Charts ───────────────────────────────────────────── --}}
<div class="row mb-4">
    <div class="col-md-5 mb-3">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-danger"></i>Dépenses par Catégorie</h6></div>
            <div class="card-body d-flex align-items-center justify-content-center">
                @if($expensesByCategory->count())
                    <canvas id="expensesChart" style="max-height:260px"></canvas>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-pie fa-2x mb-2 d-block"></i>
                        Aucune dépense ce mois
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-3">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Évolution mensuelle {{ $currentYear }}</h6></div>
            <div class="card-body">
                <canvas id="monthlyChart" style="max-height:260px"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── Budget Tracking ─────────────────────────────────── --}}
@if($budgets->count() > 0)
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-bullseye me-2 text-warning"></i>Suivi des Budgets — {{ now()->format('F Y') }}</h6>
        <a href="{{ route('budgets.index') }}" class="btn btn-sm btn-outline-primary">Gérer</a>
    </div>
    <div class="card-body">
        <div class="row">
        @foreach($budgets as $budget)
            @php
                $pct   = min(100, $budget->percentage);
                $color = $pct >= 100 ? 'danger' : ($pct >= 80 ? 'warning' : 'success');
                // NOUVEAU — burn rate icon
                $burnIcon  = $budget->burnRate > 1.2 ? '🔴' : ($budget->burnRate < 0.8 ? '🟢' : '🟡');
                $burnLabel = $budget->burnRate > 1.2 ? 'Rythme trop rapide' : ($budget->burnRate < 0.8 ? 'Bonne cadence' : 'Cadence normale');
            @endphp
            <div class="col-md-6 mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-semibold">{{ $budget->category->name }}</span>
                    <span class="small text-muted">
                        {{ number_format($budget->spent, 2) }} / {{ number_format($budget->amount, 2) }} DH
                        <span class="badge bg-{{ $color }} ms-1">{{ number_format($budget->percentage, 0) }}%</span>
                        {{-- NOUVEAU burn rate --}}
                        <span title="{{ $burnLabel }}" style="cursor:help">{{ $burnIcon }}</span>
                    </span>
                </div>
                <div class="progress" style="height:8px">
                    <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%; transition: width 0.8s ease"></div>
                </div>
                @if($budget->remaining < 0)
                    <small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Dépassé de {{ number_format(abs($budget->remaining), 2) }} DH</small>
                @else
                    <small class="text-muted">Reste : {{ number_format($budget->remaining, 2) }} DH</small>
                @endif
            </div>
        @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Top Expenses ─────────────────────────────────────── --}}
@if($topExpenseCategories->count())
<div class="card mb-4">
    <div class="card-header"><h6 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top 5 Catégories — Dépenses</h6></div>
    <div class="card-body p-0">
        @php $maxTop = $topExpenseCategories->first()->total ?? 1; @endphp
        @foreach($topExpenseCategories as $i => $cat)
        <div class="d-flex align-items-center px-4 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div class="me-3 fw-bold text-muted" style="width:24px">{{ $i + 1 }}</div>
            <div class="me-3">
                <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:{{ $cat->category->color ?? '#888' }}"></span>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between mb-1">
                    <span>{{ $cat->category->name ?? 'N/A' }}</span>
                    <span class="text-danger fw-semibold">{{ number_format($cat->total, 2) }} DH</span>
                </div>
                <div class="progress" style="height:5px">
                    <div class="progress-bar bg-danger" style="width:{{ ($cat->total / $maxTop) * 100 }}%; transition: width 0.8s ease"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── Recent Transactions ──────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Dernières Transactions</h6>
        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">
            Voir tout <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Catégorie</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $transaction)
                    <tr class="transaction-row">
                        <td>{{ $transaction->date->format('d/m/Y') }}</td>
                        <td class="text-truncate" style="max-width:200px">{{ $transaction->description ?: '—' }}</td>
                        <td>
                            <span class="badge-category" style="background-color:{{ $transaction->category->color ?? '#888' }}20;color:{{ $transaction->category->color ?? '#888' }}">
                                <i class="{{ $transaction->category->icon ?? 'fas fa-tag' }}"></i>
                                {{ $transaction->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="{{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ $transaction->type == 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} DH
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Aucune transaction ce mois</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ── Badge catégorie ── */
.badge-category{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:.82rem;font-weight:500}

/* ── NOUVEAU : animations hover cartes ── */
.card-stats{transition:transform .18s ease,box-shadow .18s ease}
.card-stats:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.10)}

/* ── NOUVEAU : hover lignes tableau ── */
.table tbody tr{transition:background-color .1s ease}

/* ── NOUVEAU : fade-in page ── */
.dashboard-fade{animation:dashFadeIn .25s ease-out}
@keyframes dashFadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}

/* ── NOUVEAU : boutons d'actions rapides ── */
.quick-action-btn{transition:transform .12s ease,box-shadow .12s ease}
.quick-action-btn:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.12)}

/* ── NOUVEAU : insight cards ── */
.insight-card{border-radius:.6rem;font-size:.85rem}

/* ── NOUVEAU : jauge SVG animation ── */
.gauge-arc{transition:stroke-dasharray .8s cubic-bezier(.4,0,.2,1)}

/* ── NOUVEAU : lignes factures à venir ── */
.upcoming-bill-row{transition:background .12s ease}
.upcoming-bill-row:hover{background:var(--color-table-row-hover,#f8faff)}
</style>
@endpush

@push('scripts')
<script>
// ══════════════════════════════════════════════════════
// NOUVEAU — Count-up animation sur les chiffres
// ══════════════════════════════════════════════════════
document.querySelectorAll('.count-up').forEach(el => {
    const target = parseFloat(el.dataset.target) || 0;
    const start  = performance.now();
    const dur    = 900;
    function step(now) {
        const p = Math.min((now - start) / dur, 1);
        const ease = 1 - Math.pow(1 - p, 3);
        el.textContent = (target * ease).toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
        if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
});

// ══════════════════════════════════════════════════════
// NOUVEAU — Sparklines 7 jours (Chart.js déjà chargé)
// ══════════════════════════════════════════════════════
document.querySelectorAll('.sparkline').forEach(canvas => {
    const values = JSON.parse(canvas.dataset.values || '[]');
    const color  = canvas.dataset.color || '#888';
    new Chart(canvas, {
        type: 'line',
        data: {
            labels: values.map((_, i) => i),
            datasets: [{
                data: values,
                borderColor: color,
                backgroundColor: color + '22',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                borderWidth: 1.5
            }]
        },
        options: {
            responsive: false,
            animation: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } }
        }
    });
});

// ══════════════════════════════════════════════════════
// Doughnut : dépenses par catégorie (identique à l'original)
// ══════════════════════════════════════════════════════
@if($expensesByCategory->count())
new Chart(document.getElementById('expensesChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($expensesByCategory->map(fn($e) => $e->category->name ?? 'N/A')) !!},
        datasets: [{
            data: {!! json_encode($expensesByCategory->pluck('total')) !!},
            backgroundColor: {!! json_encode($expensesByCategory->map(fn($e) => $e->category->color ?? '#888')) !!},
            borderWidth: 2,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: {
            legend: { position: 'right', labels: { boxWidth: 12, padding: 12 } },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${Number(ctx.raw).toLocaleString('fr-FR')} DH` } }
        }
    }
});
@endif

// ══════════════════════════════════════════════════════
// Bar + Line : évolution mensuelle (identique à l'original)
// ══════════════════════════════════════════════════════
const months   = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const incomes  = {!! json_encode(array_column($monthlyStats, 'income')) !!};
const expenses = {!! json_encode(array_column($monthlyStats, 'expense')) !!};

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [
            {
                label: 'Revenus',
                data: incomes,
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 5,
                order: 2
            },
            {
                label: 'Dépenses',
                data: expenses,
                backgroundColor: 'rgba(239,68,68,0.7)',
                borderRadius: 5,
                order: 2
            },
            {
                label: 'Solde',
                data: incomes.map((v,i) => v - expenses[i]),
                type: 'line',
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79,70,229,0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                order: 1
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('fr-FR') + ' DH' } } }
    }
});
</script>
@endpush
@endsection
