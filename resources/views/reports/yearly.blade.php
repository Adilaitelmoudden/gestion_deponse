@extends('layouts.app')

@section('title', 'Rapport Annuel')
@section('header', 'Rapport Annuel ' . $year)

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

<!-- Résumé Annuel -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Total Revenus Annuels</h6>
                <h3>{{ number_format($data['totalYearIncome'], 2) }} {{ $currency }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6>Total Dépenses Annuelles</h6>
                <h3>{{ number_format($data['totalYearExpense'], 2) }} {{ $currency }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card {{ $data['totalYearIncome'] - $data['totalYearExpense'] >= 0 ? 'bg-info' : 'bg-warning' }} text-white">
            <div class="card-body">
                <h6>Solde Annuel</h6>
                <h3>{{ number_format($data['totalYearIncome'] - $data['totalYearExpense'], 2) }} {{ $currency }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Graphique Évolution Mensuelle -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Évolution Mensuelle</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tableau Mensuel -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Détail par Mois</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mois</th>
                                <th>Revenus</th>
                                <th>Dépenses</th>
                                <th>Solde</th>
                                <th>Tendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['monthlyData'] as $month => $stats)
                            <tr>
                                <td>
                                    <strong>
                                        {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                                    </strong>
                                </td>
                                <td class="text-success">{{ number_format($stats['income'], 2) }} {{ $currency }}</td>
                                <td class="text-danger">{{ number_format($stats['expense'], 2) }} {{ $currency }}</td>
                                <td class="{{ $stats['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($stats['balance'], 2) }} {{ $currency }}
                                </td>
                                <td>
                                    @if($stats['balance'] >= 0)
                                        <i class="fas fa-arrow-up text-success"></i> Bénéfice
                                    @else
                                        <i class="fas fa-arrow-down text-danger"></i> Perte
                                    @endif
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

<!-- Résumé par Catégorie -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Résumé par Catégorie</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Dépenses par Catégorie</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['categoriesSummary']->where('type', 'expense') as $cat)
                                <tr>
                                    <td>{{ $cat->category->name }}</td>
                                    <td>{{ number_format($cat->total, 2) }} {{ $currency }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Revenus par Catégorie</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['categoriesSummary']->where('type', 'income') as $cat)
                                <tr>
                                    <td>{{ $cat->category->name }}</td>
                                    <td>{{ number_format($cat->total, 2) }} {{ $currency }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
        datasets: [
            {
                label: 'Revenus',
                data: [
                    @foreach($data['monthlyData'] as $stats)
                        {{ $stats['income'] }},
                    @endforeach
                ],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            },
            {
                label: 'Dépenses',
                data: [
                    @foreach($data['monthlyData'] as $stats)
                        {{ $stats['expense'] }},
                    @endforeach
                ],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4
            }
        ]
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
</script>
@endsection