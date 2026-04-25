@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Tableau de Bord')

@section('content')

{{-- ── Stats Cards ──────────────────────────────────────── --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card card-stats h-100" style="border-top: 3px solid #22c55e">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Revenus ce mois</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($totalIncome, 2) }} DH</div>
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
                        <div class="fs-3 fw-bold text-danger">{{ number_format($totalExpense, 2) }} DH</div>
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
                        <div class="fs-3 fw-bold {{ $balance >= 0 ? 'text-primary' : 'text-warning' }}">
                            {{ number_format($balance, 2) }} DH
                        </div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:{{ $balance >= 0 ? '#4f46e520' : '#f59e0b20' }}">
                        <i class="fas fa-wallet" style="color:{{ $balance >= 0 ? '#4f46e5' : '#f59e0b' }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- NEW: Savings rate --}}
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
                $pct = min(100, $budget->percentage);
                $color = $pct >= 100 ? 'danger' : ($pct >= 80 ? 'warning' : 'success');
            @endphp
            <div class="col-md-6 mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-semibold">{{ $budget->category->name }}</span>
                    <span class="small text-muted">
                        {{ number_format($budget->spent, 2) }} / {{ number_format($budget->amount, 2) }} DH
                        <span class="badge bg-{{ $color }} ms-1">{{ number_format($budget->percentage, 0) }}%</span>
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
                    <tr>
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
.badge-category { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:.82rem;font-weight:500; }
</style>
@endpush

@push('scripts')
<script>
// ── Doughnut: expenses by category
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

// ── Bar/Line: monthly evolution
const months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
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
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('fr-FR') + ' DH' } }
        }
    }
});
</script>
@endpush
@endsection
