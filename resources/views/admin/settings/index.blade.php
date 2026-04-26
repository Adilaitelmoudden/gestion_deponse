@extends('layouts.app')

@section('title', 'Paramètres Système')
@section('header', 'Paramètres Système')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-cog me-2 text-primary"></i>Paramètres Système</h5>
                <form action="{{ route('admin.settings.reset') }}" method="POST"
                      onsubmit="return confirm('Réinitialiser tous les paramètres aux valeurs par défaut ?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-undo me-1"></i>Réinitialiser
                    </button>
                </form>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf @method('PUT')

                    <h6 class="fw-bold mb-3" style="color:var(--color-text-secondary);text-transform:uppercase;font-size:.75rem;letter-spacing:.06em">
                        <i class="fas fa-globe me-2"></i>Application
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nom de l'application <span class="text-danger">*</span></label>
                            <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror"
                                   value="{{ old('app_name', $settings['app_name']) }}" required>
                            @error('app_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email de contact <span class="text-danger">*</span></label>
                            <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                                   value="{{ old('contact_email', $settings['contact_email']) }}" required>
                            @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message de bienvenue</label>
                            <textarea name="welcome_message" rows="2" class="form-control">{{ old('welcome_message', $settings['welcome_message']) }}</textarea>
                        </div>
                    </div>

                    <hr style="border-color:var(--color-border)">
                    <h6 class="fw-bold mb-3" style="color:var(--color-text-secondary);text-transform:uppercase;font-size:.75rem;letter-spacing:.06em">
                        <i class="fas fa-wrench me-2"></i>Maintenance & Accès
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:var(--color-surface-hover);border:1px solid var(--color-border)">
                                <div>
                                    <div class="fw-semibold" style="font-size:.9rem">Mode maintenance</div>
                                    <small style="color:var(--color-text-muted)">Bloquer l'accès aux utilisateurs</small>
                                </div>
                                <div class="ms-auto form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode"
                                           {{ old('maintenance_mode', $settings['maintenance_mode']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenanceMode"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:var(--color-surface-hover);border:1px solid var(--color-border)">
                                <div>
                                    <div class="fw-semibold" style="font-size:.9rem">Autoriser les inscriptions</div>
                                    <small style="color:var(--color-text-muted)">Permettre la création de comptes</small>
                                </div>
                                <div class="ms-auto form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="allow_registration" id="allowRegistration"
                                           {{ old('allow_registration', $settings['allow_registration']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allowRegistration"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message de maintenance</label>
                            <textarea name="maintenance_message" rows="2" class="form-control"
                                      placeholder="Message affiché pendant la maintenance...">{{ old('maintenance_message', $settings['maintenance_message']) }}</textarea>
                        </div>
                    </div>

                    <hr style="border-color:var(--color-border)">
                    <h6 class="fw-bold mb-3" style="color:var(--color-text-secondary);text-transform:uppercase;font-size:.75rem;letter-spacing:.06em">
                        <i class="fas fa-sliders-h me-2"></i>Limites & Monnaie
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Transactions max par utilisateur <span class="text-danger">*</span></label>
                            <input type="number" name="max_transactions_user" min="1" max="99999"
                                   class="form-control @error('max_transactions_user') is-invalid @enderror"
                                   value="{{ old('max_transactions_user', $settings['max_transactions_user']) }}" required>
                            @error('max_transactions_user')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Devise par défaut <span class="text-danger">*</span></label>
                            <select name="default_currency" class="form-select">
                                @foreach(['MAD' => 'MAD - Dirham marocain', 'EUR' => 'EUR - Euro', 'USD' => 'USD - Dollar', 'GBP' => 'GBP - Livre sterling'] as $code => $label)
                                    <option value="{{ $code }}" {{ old('default_currency', $settings['default_currency']) === $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Enregistrer les paramètres
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Current settings preview --}}
        <div class="card mt-3">
            <div class="card-header"><h5><i class="fas fa-eye me-2 text-secondary"></i>Paramètres actuels</h5></div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($settings as $key => $value)
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center p-2 rounded"
                             style="background:var(--color-surface-hover);font-size:.85rem">
                            <span style="color:var(--color-text-muted)">{{ $key }}</span>
                            <span class="fw-semibold">
                                @if(is_bool($value))
                                    @if($value)
                                        <span class="badge bg-success">Oui</span>
                                    @else
                                        <span class="badge bg-secondary">Non</span>
                                    @endif
                                @else
                                    {{ Str::limit((string)$value, 30) }}
                                @endif
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
