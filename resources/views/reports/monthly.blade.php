@extends('layouts.app')

@section('title', 'Rapport Mensuel')
@section('header', 'Rapport du ' . date('F Y', mktime(0, 0, 0, $month, 1, $year)))

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Nouveau Rapport
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimer
        </button>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Total Revenus</h6>
                <h3>{{ number_format($data['totalIncome'], 2) }} {{ $currency }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6>Total Dépenses</h6>
                <h3>{{ number_format($data['totalExpense'], 2) }} {{ $currency }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card {{ $data['totalIncome'] - $data['totalExpense'] >= 0 ? 'bg-info' : 'bg-warning' }} text-white">
            <div class="card-body">
                <h6>Solde</h6>
                <h3>{{ number_format($data['totalIncome'] - $data['totalExpense'], 2) }} {{ $currency }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Graphique Dépenses par Catégorie -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Dépenses par Catégorie</h5>
            </div>
            <div class="card-body">
                <canvas id="expensesChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Détail des Dépenses</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Montant</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['expensesByCategory'] as $item)
                        <tr>
                            <td>{{ $item->category->name }}</td>
                            <td>{{ number_format($item->total, 2) }} {{ $currency }}</td>
                            <td>{{ number_format(($item->total / $data['totalExpense']) * 100, 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Liste des Transactions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Détail des Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Catégorie</th>
                                <th>Type</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['transactions'] as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>{{ $transaction->description ?: '-' }}</td>
                                <td>{{ $transaction->category->name }}</td>
                                <td>
                                    <span class="badge {{ $transaction->type == 'income' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $transaction->type == 'income' ? 'Revenu' : 'Dépense' }}
                                    </span>
                                </td>
                                <td class="{{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type == 'income' ? '+' : '-' }}
                                    {{ number_format($transaction->amount, 2) }} {{ $currency }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('expensesChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($data['expensesByCategory']->pluck('category.name')) !!},
        datasets: [{
            data: {!! json_encode($data['expensesByCategory']->pluck('total')) !!},
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']
        }]
    }
});
</script>
@endsection