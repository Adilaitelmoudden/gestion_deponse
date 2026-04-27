@extends('layouts.app')

@section('title', 'Transactions Récurrentes')
@section('header', 'Transactions Récurrentes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-redo"></i> Transactions Récurrentes</h5>
        <a href="{{ route('recurring.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle
        </a>
    </div>
    <div class="card-body">
        @if($recurring->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-redo fa-4x text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Aucune transaction récurrente</h5>
                <a href="{{ route('recurring.create') }}" class="btn btn-primary mt-2">Créer une transaction récurrente</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Catégorie</th>
                            <th>Montant</th>
                            <th>Fréquence</th>
                            <th>Prochaine échéance</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recurring as $item)
                        <tr class="{{ !$item->is_active ? 'table-secondary' : '' }}">
                            <td>{{ $item->description ?: '-' }}</td>
                            <td>
                                <span class="badge-category" style="background-color: {{ $item->category->color }}20; color: {{ $item->category->color }}">
                                    <i class="{{ $item->category->icon }}"></i> {{ $item->category->name }}
                                </span>
                            </td>
                            <td class="{{ $item->type == 'income' ? 'text-success' : 'text-danger' }}">
                                <strong>{{ $item->type == 'income' ? '+' : '-' }}{{ number_format($item->amount, 2) }} {{ $currency }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item->frequency_label }}</span>
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="{{ $item->next_due_date->isPast() ? 'text-danger fw-bold' : '' }}">
                                        {{ $item->next_due_date->format('d/m/Y') }}
                                        @if($item->next_due_date->isToday())
                                            <span class="badge bg-warning text-dark">Aujourd'hui</span>
                                        @elseif($item->next_due_date->isPast())
                                            <span class="badge bg-danger">En retard</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_active)
                                <form action="{{ route('recurring.execute', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success btn-action"
                                            title="Enregistrer maintenant"
                                            onclick="return confirm('Enregistrer cette transaction maintenant ?')">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('recurring.edit', $item) }}" class="btn btn-sm btn-warning btn-action">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('recurring.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-action"
                                            onclick="return confirm('Supprimer cette transaction récurrente ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
