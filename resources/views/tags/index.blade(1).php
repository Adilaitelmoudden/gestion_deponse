@extends('layouts.app')

@section('title', 'Tags')
@section('header', 'Gestion des Tags')

@section('content')
<div class="row">
    {{-- ── Create tag ── --}}
    <div class="col-md-4 mb-4">
        <div class="card animate-fade-in">
            <div class="card-header">
                <h5><i class="fas fa-tag me-2"></i>Nouveau Tag</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tags.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nom du tag *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               placeholder="Ex: Urgent, Personnel, Pro…" value="{{ old('name') }}" required maxlength="50">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Couleur</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color" class="form-control form-control-color"
                                   value="{{ old('color', '#6366f1') }}" style="width:50px;height:38px;">
                            <span class="text-muted small">Choisissez une couleur distinctive</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-ripple">
                        <i class="fas fa-plus me-1"></i> Créer le tag
                    </button>
                </form>
            </div>
        </div>

        {{-- Tips --}}
        <div class="card mt-3 animate-fade-in" style="animation-delay:.1s">
            <div class="card-body">
                <h6 class="text-muted mb-2"><i class="fas fa-lightbulb me-1 text-warning"></i> Conseils</h6>
                <ul class="small text-muted ps-3 mb-0">
                    <li>Les tags permettent de classer vos transactions au-delà des catégories</li>
                    <li>Exemples : <strong>Urgent</strong>, <strong>Pro</strong>, <strong>Famille</strong></li>
                    <li>Filtrez vos transactions par tag depuis la liste</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ── Tags list ── --}}
    <div class="col-md-8 mb-4">
        <div class="card animate-fade-in" style="animation-delay:.05s">
            <div class="card-header">
                <h5><i class="fas fa-tags me-2"></i>Mes Tags <span class="badge bg-secondary ms-1">{{ $tags->count() }}</span></h5>
            </div>
            <div class="card-body">
                @if($tags->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-tags fa-3x mb-3 d-block opacity-25"></i>
                        Aucun tag créé. Commencez par en ajouter un !
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Tag</th>
                                    <th class="text-center">Transactions</th>
                                    <th>Modifier</th>
                                    <th>Supprimer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tags as $tag)
                                <tr class="animate-row-in" style="animation-delay:{{ $loop->index * 0.04 }}s">
                                    <td>
                                        <span class="tag-pill" style="background:{{ $tag->color }}22;color:{{ $tag->color }};border:1px solid {{ $tag->color }}55;">
                                            <i class="fas fa-circle" style="font-size:.5rem;"></i>
                                            {{ $tag->name }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $tag->transactions_count }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning btn-ripple"
                                                data-bs-toggle="modal" data-bs-target="#editTag{{ $tag->id }}">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <form action="{{ route('tags.destroy', $tag) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer ce tag ? Il sera retiré de toutes les transactions.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-ripple">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit modal --}}
                                <div class="modal fade" id="editTag{{ $tag->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form action="{{ route('tags.update', $tag) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Modifier le tag</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nom</label>
                                                        <input type="text" name="name" class="form-control"
                                                               value="{{ $tag->name }}" required maxlength="50">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Couleur</label>
                                                        <input type="color" name="color" class="form-control form-control-color"
                                                               value="{{ $tag->color }}" style="width:50px;height:38px;">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
