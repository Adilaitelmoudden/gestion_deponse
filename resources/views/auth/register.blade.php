@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')
<div class="card auth-card">
    <div class="auth-header">
        <i class="fas fa-user-plus fa-3x mb-3"></i>
        <h3>{{ $adminSettings['app_name'] ?? 'Gestion des Dépenses' }}</h3>
        <p class="mb-0">Inscrivez-vous gratuitement</p>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Nom complet</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="name" id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" required>
                </div>
                @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" id="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" required>
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" 
                           class="form-control @error('password') is-invalid @enderror" required>
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmer mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="form-control" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-user-plus"></i> S'inscrire
            </button>
            
            <p class="text-center mb-0">
                Déjà un compte ? 
                <a href="{{ route('login') }}">Se connecter</a>
            </p>
        </form>
    </div>
</div>
@endsection