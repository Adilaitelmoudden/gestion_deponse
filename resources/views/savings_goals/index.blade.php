@extends('layouts.app')

@section('title', 'Objectifs d\'Épargne')
@section('header', 'Mes Objectifs d\'Épargne')

@section('content')
<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white" style="background: linear-gradient(135deg,#667eea,#764ba2);">
            <div class="card-body">
                <h6><i class="fas fa-piggy-bank"></i> Total Épargné</h6>
                <h3>{{ number_format($totalSaved, 2) }} DH</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white" style="background: linear-gradient(135deg,#f093fb,#f5576c);">
            <div class="card-body">
                <h6><i class="fas fa-bullseye"></i> Total Ciblé</h6>
                <h3>{{ number_format($totalTargeted, 2) }} DH</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white" style="background: linear-gradient(135deg,#4facfe,#00f2fe);">
            <div class="card-body">
                <h6><i class="fas fa-check-circle"></i> Objectifs Atteints</h6>
                <h3>{{ $completedCount }} / {{ $goals->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-piggy-bank"></i> Objectifs d'Épargne</h5>
        <a href="{{ route('savings_goals.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvel Objectif
        </a>
    </div>
    <div class="card-body">
        @if($goals->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-piggy-bank fa-4x text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Aucun objectif d'épargne</h5>
                <a href="{{ route('savings_goals.create') }}" class="btn btn-primary mt-2">
                    Créer mon premier objectif
                </a>
            </div>
        @else
            <div class="row">
                @foreach($goals as $goal)
                <div class="col-md-6 mb-4">
                    <div class="card h-100 {{ $goal->is_completed ? 'border-success' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">
                                    @if($goal->is_completed)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-piggy-bank text-primary"></i>
                                    @endif
                                    {{ $goal->name }}
                                </h6>
                                <div>
                                    <a href="{{ route('savings_goals.show', $goal) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('savings_goals.edit', $goal) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('savings_goals.destroy', $goal) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Supprimer cet objectif ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <small>{{ number_format($goal->current_amount, 2) }} DH</small>
                                    <small>{{ number_format($goal->target_amount, 2) }} DH</small>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar {{ $goal->is_completed ? 'bg-success' : 'bg-primary' }}"
                                         style="width: {{ $goal->percentage }}%">
                                        {{ $goal->percentage }}%
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Restant: <strong>{{ number_format($goal->remaining, 2) }} DH</strong>
                                </small>
                                @if($goal->deadline)
                                    <small class="{{ isset($goal->days_left) && $goal->days_left < 30 ? 'text-danger' : 'text-muted' }}">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $goal->deadline->format('d/m/Y') }}
                                        @if(isset($goal->days_left))
                                            ({{ $goal->days_left > 0 ? $goal->days_left . 'j restants' : 'Expiré' }})
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
