@extends('layouts.app')

@section('title', 'Budgets')
@section('header', 'Gestion des Budgets')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-calendar-alt"></i> Sélectionner la période</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('budgets.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Mois</label>
                <select name="month" class="form-control">
                    @foreach($availableMonths as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Année</label>
                <select name="year" class="form-control">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Voir
                </button>
            </div>
            
            <div class="col-md-4 d-flex align-items-end justify-content-end">
                <a href="{{ route('budgets.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Ajouter un budget
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-chart-pie"></i> Budgets - {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</h5>
    </div>
    <div class="card-body">
        @if($budgets->count() > 0)
            @foreach($budgets as $budget)
                @php
                    $progressClass = $budget->percentage >= 100 ? 'bg-danger' : ($budget->percentage >= 80 ? 'bg-warning' : 'bg-success');
                    $remainingClass = $budget->remaining >= 0 ? 'text-success' : 'text-danger';
                @endphp
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <i class="{{ $budget->category->icon }}"></i>
                            <strong>{{ $budget->category->name }}</strong>
                        </div>
                        <div>
                            <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce budget ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar {{ $progressClass }}" 
                             role="progressbar" 
                             style="width: {{ min(100, $budget->percentage) }}%"
                             aria-valuenow="{{ $budget->percentage }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ number_format($budget->percentage, 1) }}%
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-md-4">
                            <small class="text-muted">Budget</small><br>
                            <strong>{{ number_format($budget->amount, 2) }} {{ $currency }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Dépensé</small><br>
                            <strong class="text-danger">{{ number_format($budget->spent, 2) }} {{ $currency }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Restant</small><br>
                            <strong class="{{ $remainingClass }}">{{ number_format($budget->remaining, 2) }} {{ $currency }}</strong>
                        </div>
                    </div>
                </div>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @else
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-3x text-muted mb-3 d-block"></i>
                <p>Aucun budget défini pour cette période.</p>
                <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer un budget
                </a>
            </div>
        @endif
    </div>
</div>
@endsection