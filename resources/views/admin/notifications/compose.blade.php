@extends('layouts.app')

@section('title', 'Envoyer une Notification')
@section('header', 'Envoyer une Notification')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-paper-plane me-2 text-primary"></i>Composer une Notification</h5>
                <a href="{{ route('admin.notifications.history') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-history me-1"></i>Historique
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.notifications.send') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Destinataire</label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                            <option value="">📢 Tous les utilisateurs</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small style="color:var(--color-text-muted)">Laissez vide pour envoyer à tous les utilisateurs.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="form-control @error('title') is-invalid @enderror"
                               placeholder="Titre de la notification" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" rows="5"
                                  class="form-control @error('message') is-invalid @enderror"
                                  placeholder="Corps du message..." required>{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Envoyer
                        </button>
                        <a href="{{ route('admin.notifications.history') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
