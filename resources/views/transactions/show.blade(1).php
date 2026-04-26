@extends('layouts.app')

@section('title', 'Détail Transaction')
@section('header', 'Détail de la Transaction')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informations</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Type :</div>
                    <div class="col-md-8">
                        <span class="badge {{ $transaction->type == 'income' ? 'bg-success' : 'bg-danger' }} fs-6">
                            <i class="fas {{ $transaction->type == 'income' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ $transaction->type == 'income' ? 'Revenu' : 'Dépense' }}
                        </span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Catégorie :</div>
                    <div class="col-md-8">
                        <span class="badge-category" style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }}; padding: 8px 15px;">
                            <i class="{{ $transaction->category->icon }}"></i>
                            {{ $transaction->category->name }}
                        </span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Montant :</div>
                    <div class="col-md-8">
                        <h3 class="{{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type == 'income' ? '+' : '-' }}
                            {{ number_format($transaction->amount, 2) }} DH
                        </h3>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Description :</div>
                    <div class="col-md-8">
                        {{ $transaction->description ?: '-' }}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Date :</div>
                    <div class="col-md-8">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $transaction->date->format('d/m/Y') }}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Créé le :</div>
                    <div class="col-md-8">
                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <div>
                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer cette transaction ?')">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection