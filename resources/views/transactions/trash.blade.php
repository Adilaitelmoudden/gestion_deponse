@extends('layouts.app')

@section('title', 'Corbeille')
@section('header', 'Corbeille des Transactions')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-trash-alt"></i> Corbeille</h5>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux transactions
        </a>
    </div>
    <div class="card-body">
        @if($transactions->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3 d-block"></i>
                <h5 class="text-muted">La corbeille est vide</h5>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i>
                Les transactions supprimées peuvent être restaurées. La suppression définitive est irréversible.
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Catégorie</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Supprimé le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date->format('d/m/Y') }}</td>
                            <td class="text-muted"><s>{{ $transaction->description ?: '-' }}</s></td>
                            <td>{{ $transaction->category->name ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $transaction->type == 'income' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $transaction->type == 'income' ? 'Revenu' : 'Dépense' }}
                                </span>
                            </td>
                            <td class="{{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                                {{ number_format($transaction->amount, 2) }} {{ $currency }}
                            </td>
                            <td class="text-muted">{{ $transaction->deleted_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('transactions.restore', $transaction->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Restaurer">
                                        <i class="fas fa-undo"></i> Restaurer
                                    </button>
                                </form>
                                <form action="{{ route('transactions.force-delete', $transaction->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Supprimer définitivement ? Cette action est irréversible.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer définitivement">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
