@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Tableau de Bord')

@section('content')
<!-- Statistiques Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card card-stats bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Revenus</h6>
                        <h3 class="mb-0">{{ number_format($totalIncome, 2) }} DH</h3>
                    </div>
                    <i class="fas fa-arrow-up fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card card-stats bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Dépenses</h6>
                        <h3 class="mb-0">{{ number_format($totalExpense, 2) }} DH</h3>
                    </div>
                    <i class="fas fa-arrow-down fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card card-stats {{ $balance >= 0 ? 'bg-success' : 'bg-warning' }} text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Solde</h6>
                        <h3 class="mb-0">{{ number_format($balance, 2) }} DH</h3>
                    </div>
                    <i class="fas fa-wallet fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5>Dépenses par Catégorie</h5>
            </div>
            <div class="card-body">
                <canvas id="expensesChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5>Top 5 des Dépenses</h5>
            </div>
            <div class="card-body">
                <canvas id="topExpensesChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Budgets Alert -->
@if($budgets->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Suivi des Budgets</h5>
            </div>
            <div class="card-body">
                @foreach($budgets as $budget)
                    @php
                        $progressClass = $budget->percentage >= 100 ? 'bg-danger' : ($budget->percentage >= 80 ? 'bg-warning' : 'bg-success');
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $budget->category->name }}</span>
                            <span>{{ number_format($budget->spent, 2) }} / {{ number_format($budget->amount, 2) }} DH ({{ number_format($budget->percentage, 1) }}%)</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                                 style="width: {{ min(100, $budget->percentage) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- Dernières Transactions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Dernières Transactions</h5>
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-primary">
                    Voir tout <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
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
                                <td>{{ $transaction->description ?: '-' }}</td>
                                <td>
                                    <span class="badge-category" style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }}">
                                        <i class="{{ $transaction->category->icon }}"></i>
                                        {{ $transaction->category->name }}
                                    </span>
                                </td>
                                <td class="{{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                                    <strong>
                                        {{ $transaction->type == 'income' ? '+' : '-' }}
                                        {{ number_format($transaction->amount, 2) }} DH
                                    </strong>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Aucune transaction</td>
                            </tr>
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
// Graphique des dépenses par catégorie
const expensesCtx = document.getElementById('expensesChart').getContext('2d');
new Chart(expensesCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($expensesByCategory->pluck('category.name')) !!},
        datasets: [{
            data: {!! json_encode($expensesByCategory->pluck('total')) !!},
            backgroundColor: {!! json_encode($expensesByCategory->pluck('category.color')) !!},
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Graphique Top 5 dépenses
const topCtx = document.getElementById('topExpensesChart').getContext('2d');
new Chart(topCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($topExpenseCategories->pluck('category.name')) !!},
        datasets: [{
            label: 'Montant (DH)',
            data: {!! json_encode($topExpenseCategories->pluck('total')) !!},
            backgroundColor: '#ff6384',
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Montant (DH)'
                }
            }
        }
    }
});
</script>
@endpush