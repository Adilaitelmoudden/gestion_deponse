@extends('layouts.app')

@section('title', 'Rapport par Catégorie')
@section('header', 'Analyse par Catégorie - ' . $year)

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

<div class="row">
    @foreach($data['categories'] as $category)
        @php
            $totalAmount = $category->transactions->sum('amount');
            $percentage = $category->type == 'expense' && $data['totalYearExpense'] > 0 
                ? ($totalAmount / $data['totalYearExpense']) * 100 
                : ($category->type == 'income' && $data['totalYearIncome'] > 0 
                    ? ($totalAmount / $data['totalYearIncome']) * 100 
                    : 0);
        @endphp
        
        @if($totalAmount > 0)
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header" style="background-color: {{ $category->color }}20;">
                    <h5>
                        <i class="{{ $category->icon }}"></i>
                        {{ $category->name }}
                        <small class="text-muted">({{ $category->type == 'expense' ? 'Dépense' : 'Revenu' }})</small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h3>{{ number_format($totalAmount, 2) }} DH</h3>
                            <small>{{ number_format($percentage, 1) }}% du total</small>
                        </div>
                        <div class="col-6">
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar" style="width: {{ min(100, $percentage) }}%; background-color: {{ $category->color }}"></div>
                            </div>
                        </div>
                    </div>
                    
                    @if($category->transactions->count() > 0)
                    <hr>
                    <h6>Dernières transactions :</h6>
                    <ul class="list-group list-group-flush">
                        @foreach($category->transactions->take(5) as $transaction)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $transaction->date->format('d/m/Y') }}</span>
                            <span>{{ $transaction->description ?: '-' }}</span>
                            <span class="{{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                                {{ number_format($transaction->amount, 2) }} DH
                            </span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
        @endif
    @endforeach
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Résumé Global</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h6>Total Revenus</h6>
                        <h4 class="text-success">{{ number_format($data['totalYearIncome'], 2) }} DH</h4>
                    </div>
                    <div class="col-md-4">
                        <h6>Total Dépenses</h6>
                        <h4 class="text-danger">{{ number_format($data['totalYearExpense'], 2) }} DH</h4>
                    </div>
                    <div class="col-md-4">
                        <h6>Solde</h6>
                        <h4 class="{{ $data['totalYearIncome'] - $data['totalYearExpense'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($data['totalYearIncome'] - $data['totalYearExpense'], 2) }} DH
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection