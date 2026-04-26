@extends('layouts.app')

@section('title', $savingsGoal->name)
@section('header', 'Détail de l\'Objectif')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    @if($savingsGoal->is_completed)
                        <i class="fas fa-check-circle text-success"></i>
                    @else
                        <i class="fas fa-piggy-bank text-primary"></i>
                    @endif
                    {{ $savingsGoal->name }}
                </h5>
                @if($savingsGoal->is_completed)
                    <span class="badge bg-success">Objectif atteint! 🎉</span>
                @endif
            </div>
            <div class="card-body">
                @if($savingsGoal->description)
                    <p class="text-muted">{{ $savingsGoal->description }}</p>
                @endif

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold">{{ number_format($savingsGoal->current_amount, 2) }} DH</span>
                        <span class="text-muted">/ {{ number_format($savingsGoal->target_amount, 2) }} DH</span>
                    </div>
                    <div class="progress" style="height:18px;">
                        <div class="progress-bar {{ $savingsGoal->is_completed ? 'bg-success' : 'bg-primary' }} progress-bar-striped"
                             style="width: {{ $savingsGoal->percentage }}%">
                            {{ $savingsGoal->percentage }}%
                        </div>
                    </div>
                    <small class="text-muted">Restant: {{ number_format($savingsGoal->remaining, 2) }} DH</small>
                </div>

                @if($savingsGoal->deadline)
                    <p><i class="fas fa-calendar-alt text-primary"></i>
                        Date limite: <strong>{{ $savingsGoal->deadline->format('d/m/Y') }}</strong>
                    </p>
                @endif

                @if(!$savingsGoal->is_completed)
                <!-- Deposit form -->
                <hr>
                <h6><i class="fas fa-plus-circle text-success"></i> Verser de l'argent</h6>
                <form method="POST" action="{{ route('savings_goals.deposit', $savingsGoal) }}" class="mb-3">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="number" name="amount" step="0.01" min="0.01" class="form-control"
                               placeholder="Montant (DH)" required>
                        <button class="btn btn-success" type="submit"><i class="fas fa-plus"></i> Verser</button>
                    </div>
                    <input type="text" name="note" class="form-control form-control-sm" placeholder="Note (optionnel)">
                </form>
                @endif

                <!-- Withdraw form -->
                @if($savingsGoal->current_amount > 0)
                <h6><i class="fas fa-minus-circle text-warning"></i> Retirer de l'argent</h6>
                <form method="POST" action="{{ route('savings_goals.withdraw', $savingsGoal) }}">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="number" name="amount" step="0.01" min="0.01"
                               max="{{ $savingsGoal->current_amount }}" class="form-control"
                               placeholder="Montant (DH)" required>
                        <button class="btn btn-warning" type="submit"><i class="fas fa-minus"></i> Retirer</button>
                    </div>
                    <input type="text" name="note" class="form-control form-control-sm" placeholder="Note (optionnel)">
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-7 mb-4">
        <div class="card h-100">
            <div class="card-header"><h5><i class="fas fa-history"></i> Historique des Opérations</h5></div>
            <div class="card-body">
                @if(empty($history))
                    <p class="text-muted text-center py-4">Aucune opération enregistrée.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Opération</th>
                                    <th>Montant</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_reverse($history) as $entry)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d/m/Y') }}</td>
                                    <td>
                                        @if($entry['type'] == 'deposit')
                                            <span class="badge bg-success"><i class="fas fa-plus"></i> Versement</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="fas fa-minus"></i> Retrait</span>
                                        @endif
                                    </td>
                                    <td class="{{ $entry['type'] == 'deposit' ? 'text-success' : 'text-warning' }}">
                                        <strong>{{ $entry['type'] == 'deposit' ? '+' : '-' }}{{ number_format($entry['amount'], 2) }} DH</strong>
                                    </td>
                                    <td class="text-muted">{{ $entry['note'] ?: '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<a href="{{ route('savings_goals.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Retour
</a>
@endsection
