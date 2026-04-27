@extends('layouts.app')

@section('title', 'Paramètres Système')
@section('header', 'Paramètres Système')

@section('content')

{{-- ══════════ TOASTS ══════════ --}}
@if(session('success'))
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100">
    <div class="toast align-items-center text-white border-0 show" role="alert"
         style="background:linear-gradient(135deg,#10b981,#059669);border-radius:12px;box-shadow:0 8px 24px rgba(16,185,129,.35)">
        <div class="d-flex">
            <div class="toast-body fw-semibold">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100">
    <div class="toast align-items-center text-white border-0 show" role="alert"
         style="background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:12px;box-shadow:0 8px 24px rgba(239,68,68,.35)">
        <div class="d-flex">
            <div class="toast-body fw-semibold">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

<style>
.settings-hero {
    background: linear-gradient(135deg, var(--color-sidebar-from), var(--color-sidebar-to));
    border-radius: 16px; padding: 24px 28px; margin-bottom: 24px;
    display: flex; align-items: center; gap: 16px;
    box-shadow: 0 8px 32px rgba(79,70,229,.25);
}
.settings-hero-icon {
    width: 54px; height: 54px; background: rgba(255,255,255,.18);
    border-radius: 14px; display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; color: #fff; flex-shrink: 0;
}
.settings-hero h4 { color: #fff; margin: 0; font-weight: 700; font-size: 1.2rem; }
.settings-hero p  { color: rgba(255,255,255,.75); margin: 0; font-size: .875rem; }
.settings-card {
    background: var(--color-surface); border: 1px solid var(--color-border);
    border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-sm); margin-bottom: 16px;
}
.settings-card-header {
    padding: 16px 20px; border-bottom: 1px solid var(--color-border);
    display: flex; align-items: center; gap: 12px; background: var(--color-card-header-bg);
}
.settings-card-header-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: .875rem;
}
.settings-card-body { padding: 20px; }
.section-label {
    font-size: .72rem; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; color: var(--color-text-muted);
    margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
}
.section-label::after { content: ''; flex: 1; height: 1px; background: var(--color-border); }
.form-control, .form-select {
    background: var(--color-input-bg); border-color: var(--color-input-border);
    color: var(--color-input-text); border-radius: 10px;
}
.form-control:focus, .form-select:focus {
    background: var(--color-input-bg); color: var(--color-input-text);
    border-color: var(--color-input-focus); box-shadow: 0 0 0 3px rgba(79,70,229,.12);
}
.form-label { font-size: .875rem; font-weight: 500; color: var(--color-text-secondary); margin-bottom: 6px; }
.save-bar {
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    padding: 16px 20px; background: var(--color-surface);
    border: 1px solid var(--color-border); border-radius: 14px;
    position: sticky; bottom: 16px; box-shadow: var(--shadow-md); backdrop-filter: blur(8px);
}
.save-bar small { color: var(--color-text-muted); font-size: .8rem; }
</style>

<div class="settings-hero">
    <div class="settings-hero-icon"><i class="fas fa-sliders-h"></i></div>
    <div>
        <h4>Paramètres Système</h4>
        <p>Limites de transactions et devise par défaut</p>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf @method('PUT')

    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-header-icon" style="background:rgba(245,158,11,.12);color:#f59e0b">
                <i class="fas fa-sliders-h"></i>
            </div>
            <div>
                <div class="fw-semibold" style="color:var(--color-text-primary)">Limites & Monnaie</div>
                <div style="font-size:.8rem;color:var(--color-text-muted)">Définissez les quotas et la devise par défaut</div>
            </div>
        </div>
        <div class="settings-card-body">
            <div class="section-label"><i class="fas fa-tachometer-alt me-1"></i>Limite utilisateur</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Transactions max par utilisateur <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--color-surface-hover);border-color:var(--color-input-border)">
                            <i class="fas fa-exchange-alt" style="color:var(--color-text-muted)"></i>
                        </span>
                        <input type="number" name="max_transactions_user" min="1" max="99999"
                               class="form-control @error('max_transactions_user') is-invalid @enderror"
                               value="{{ old('max_transactions_user', $settings['max_transactions_user']) }}" required>
                        <span class="input-group-text" style="background:var(--color-surface-hover);border-color:var(--color-input-border);color:var(--color-text-muted);font-size:.8rem">
                            transactions
                        </span>
                    </div>
                    @error('max_transactions_user')
                        <div class="text-danger mt-1" style="font-size:.8rem">{{ $message }}</div>
                    @enderror
                    <div class="mt-2">
                        <small style="color:var(--color-text-muted)">Rapide :</small>
                        @foreach([100, 500, 1000, 5000] as $preset)
                        <button type="button" class="btn btn-sm ms-1"
                                style="font-size:.75rem;padding:2px 10px;border-radius:6px;background:var(--color-surface-hover);border:1px solid var(--color-border);color:var(--color-text-secondary)"
                                onclick="document.querySelector('[name=max_transactions_user]').value={{ $preset }}">
                            {{ number_format($preset) }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Devise par défaut <span class="text-danger">*</span></label>
                    <select name="default_currency" class="form-select">
                        @php
                            $currencies = [
                                'MAD' => ['label' => 'MAD — Dirham marocain',  'flag' => '🇲🇦'],
                                'EUR' => ['label' => 'EUR — Euro',              'flag' => '🇪🇺'],
                                'USD' => ['label' => 'USD — Dollar américain',  'flag' => '🇺🇸'],
                                'GBP' => ['label' => 'GBP — Livre sterling',    'flag' => '🇬🇧'],
                                'CAD' => ['label' => 'CAD — Dollar canadien',   'flag' => '🇨🇦'],
                                'CHF' => ['label' => 'CHF — Franc suisse',      'flag' => '🇨🇭'],
                                'SAR' => ['label' => 'SAR — Riyal saoudien',    'flag' => '🇸🇦'],
                                'AED' => ['label' => 'AED — Dirham UAE',        'flag' => '🇦🇪'],
                            ];
                        @endphp
                        @foreach($currencies as $code => $info)
                            <option value="{{ $code }}" {{ old('default_currency', $settings['default_currency']) === $code ? 'selected' : '' }}>
                                {{ $info['flag'] }} {{ $info['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <div class="mt-2 p-2 rounded-3" style="background:var(--color-surface-hover);border:1px solid var(--color-border)">
                        <div class="d-flex align-items-center justify-content-between">
                            <small style="color:var(--color-text-muted)">Taux actuel (1 MAD) :</small>
                            <span class="fw-semibold" style="font-size:.875rem;color:var(--color-text-primary)">
                                = {{ number_format($rateInfo['rate'], 4) }} {{ $rateInfo['to'] }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <small style="color:var(--color-text-muted)">Exemple (1 000 MAD) :</small>
                            <span class="fw-semibold" style="color:#4f46e5;font-size:.875rem">
                                ≈ {{ $rateInfo['example'] }}
                            </span>
                        </div>
                        <div class="mt-1" style="font-size:.72rem;color:var(--color-text-muted)">
                            <i class="fas fa-info-circle me-1"></i>Taux mis à jour automatiquement (cache 6h)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Preserve other settings as hidden fields so update() doesn't lose them --}}
    <input type="hidden" name="app_name"            value="{{ $settings['app_name'] }}">
    <input type="hidden" name="contact_email"       value="{{ $settings['contact_email'] }}">
    <input type="hidden" name="welcome_message"     value="{{ $settings['welcome_message'] }}">
    <input type="hidden" name="maintenance_message" value="{{ $settings['maintenance_message'] }}">

    <div class="save-bar">
        <small><i class="fas fa-info-circle me-1"></i>Les modifications s'appliquent immédiatement après sauvegarde.</small>
        <div class="d-flex gap-2">
            <button type="reset" class="btn btn-sm"
                    style="border-radius:8px;border:1px solid var(--color-border);background:transparent;color:var(--color-text-secondary)">
                <i class="fas fa-times me-1"></i>Annuler
            </button>
            <button type="submit" class="btn btn-primary btn-sm" style="border-radius:8px;padding:8px 20px">
                <i class="fas fa-save me-1"></i>Enregistrer
            </button>
        </div>
    </div>

</form>

<script>
setTimeout(() => {
    document.querySelectorAll('.toast').forEach(t => {
        bootstrap.Toast.getOrCreateInstance(t).hide();
    });
}, 4000);
</script>

@endsection
