@extends('layouts.app')

@section('title', 'Notifications')
@section('header', 'Notifications')

@section('content')

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="notif-stat unread-stat">
            <i class="fas fa-bell-ring"></i>
            <div>
                <div class="ns-num">{{ $notifications->where('is_read', false)->count() }}</div>
                <div class="ns-lbl">Non lues</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="notif-stat read-stat">
            <i class="fas fa-check-double"></i>
            <div>
                <div class="ns-num">{{ $notifications->where('is_read', true)->count() }}</div>
                <div class="ns-lbl">Lues</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="notif-stat total-stat">
            <i class="fas fa-bell"></i>
            <div>
                <div class="ns-num">{{ $notifications->total() }}</div>
                <div class="ns-lbl">Total</div>
            </div>
        </div>
    </div>
</div>

<div class="card notif-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-bell me-2 text-warning"></i> Mes Notifications
        </h5>
        <div class="d-flex gap-2">
            @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('notifications.read', 'all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-check-double me-1"></i> Tout lire
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="card-body p-0">
        @forelse($notifications as $i => $notif)
        <div class="notif-row {{ $notif->is_read ? '' : 'is-unread' }}" style="--i:{{ $i }}">

            {{-- Icon --}}
            <div class="notif-ico
                @if(str_contains($notif->title, '🔴') || str_contains($notif->message, 'dépassé')) ico-danger
                @elseif(str_contains($notif->title, '🟡') || str_contains($notif->message, 'attention')) ico-warning
                @elseif(str_contains($notif->message, 'objectif') || str_contains($notif->message, 'atteint')) ico-success
                @else ico-info
                @endif
            ">
                @if(str_contains($notif->title, '🔴') || str_contains($notif->message, 'dépassé'))
                    <i class="fas fa-triangle-exclamation"></i>
                @elseif(str_contains($notif->title, '🟡') || str_contains($notif->message, 'attention'))
                    <i class="fas fa-circle-exclamation"></i>
                @elseif(str_contains($notif->message, 'objectif') || str_contains($notif->message, 'atteint'))
                    <i class="fas fa-trophy"></i>
                @else
                    <i class="fas fa-info"></i>
                @endif
            </div>

            {{-- Content --}}
            <div class="notif-content">
                <div class="notif-title-row">
                    <span class="notif-title">{{ $notif->title }}</span>
                    @if(!$notif->is_read)
                    <span class="notif-dot"></span>
                    @endif
                </div>
                <p class="notif-msg">{{ $notif->message }}</p>
                <span class="notif-time">
                    <i class="fas fa-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                </span>
            </div>

            {{-- Actions --}}
            <div class="notif-btns">
                @if(!$notif->is_read)
                <form action="{{ route('notifications.read', $notif) }}" method="POST">
                    @csrf
                    <button type="submit" class="nb nb-read" title="Marquer comme lu">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
                @endif
                <form action="{{ route('notifications.destroy', $notif) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="nb nb-del" title="Supprimer"
                        onclick="return confirm('Supprimer cette notification ?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="notif-empty">
            <div class="notif-empty-icon">
                <i class="fas fa-bell-slash"></i>
            </div>
            <p>Aucune notification pour le moment</p>
            <small>Les alertes de budget et d'objectifs apparaîtront ici</small>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="card-footer">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
/* ── Stats ────────────────────────────────── */
.notif-stat {
    display: flex; align-items: center; gap: 14px;
    padding: 16px 20px; border-radius: 14px;
    font-size: 22px;
    animation: slideUp .4s ease both;
}
.unread-stat { background:linear-gradient(135deg,#fef3c7,#fde68a); color:#92400e; }
.read-stat   { background:linear-gradient(135deg,#dcfce7,#bbf7d0); color:#15803d; }
.total-stat  { background:linear-gradient(135deg,#ede9fe,#ddd6fe); color:#6d28d9; }
[data-theme="dark"] .unread-stat { background:linear-gradient(135deg,#451a0344,#78350f44); }
[data-theme="dark"] .read-stat   { background:linear-gradient(135deg,#052e1644,#14532d44); }
[data-theme="dark"] .total-stat  { background:linear-gradient(135deg,#2e106544,#4c1d9544); }
.ns-num { font-size:26px; font-weight:700; line-height:1; }
.ns-lbl { font-size:12px; opacity:.7; margin-top:2px; }

/* ── Notification Rows ───────────────────── */
.notif-row {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--color-border);
    transition: background .2s ease;
    animation: fadeSlide .3s ease both;
    animation-delay: calc(var(--i) * 40ms);
}
.notif-row:last-child { border-bottom: none; }
.notif-row:hover { background: var(--color-surface-hover); }
.notif-row.is-unread {
    background: color-mix(in srgb, #4f46e5 5%, transparent);
    border-left: 3px solid #4f46e5;
}
.notif-row.is-unread:hover { background: color-mix(in srgb, #4f46e5 9%, transparent); }

/* ── Icons ───────────────────────────────── */
.notif-ico {
    width:40px; height:40px;
    border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    font-size:16px; flex-shrink:0;
}
.ico-danger  { background:#fee2e2; color:#dc2626; }
.ico-warning { background:#fef3c7; color:#d97706; }
.ico-success { background:#dcfce7; color:#16a34a; }
.ico-info    { background:#ede9fe; color:#7c3aed; }
[data-theme="dark"] .ico-danger  { background:#450a0a44; color:#f87171; }
[data-theme="dark"] .ico-warning { background:#451a0344; color:#fcd34d; }
[data-theme="dark"] .ico-success { background:#052e1644; color:#4ade80; }
[data-theme="dark"] .ico-info    { background:#2e106544; color:#a78bfa; }

/* ── Content ─────────────────────────────── */
.notif-content { flex:1; min-width:0; }
.notif-title-row { display:flex; align-items:center; gap:8px; margin-bottom:4px; }
.notif-title { font-weight:600; font-size:14px; color:var(--color-text-primary); }
.notif-dot {
    width:8px; height:8px; border-radius:50%;
    background:#4f46e5; flex-shrink:0;
    animation: pulse 1.5s infinite;
}
.notif-msg { font-size:13px; color:var(--color-text-secondary); margin:0 0 5px; }
.notif-time { font-size:11px; color:var(--color-text-muted); }

/* ── Action Buttons ──────────────────────── */
.notif-btns { display:flex; gap:6px; flex-shrink:0; }
.nb {
    width:32px; height:32px; border-radius:9px; border:none;
    display:flex; align-items:center; justify-content:center;
    font-size:13px; cursor:pointer;
    transition: transform .15s ease, opacity .15s ease;
}
.nb:hover { transform:scale(1.15); }
.nb-read { background:#dcfce7; color:#16a34a; }
.nb-del  { background:#fee2e2; color:#dc2626; }
[data-theme="dark"] .nb-read { background:#052e1644; color:#4ade80; }
[data-theme="dark"] .nb-del  { background:#450a0a44; color:#f87171; }

/* ── Empty State ─────────────────────────── */
.notif-empty {
    text-align:center; padding:60px 20px;
    color:var(--color-text-muted);
}
.notif-empty-icon {
    width:80px; height:80px; border-radius:50%;
    background:var(--color-surface-hover);
    display:flex; align-items:center; justify-content:center;
    font-size:32px; margin:0 auto 16px;
}
.notif-empty p { font-size:16px; font-weight:500; margin-bottom:4px; }
.notif-empty small { font-size:13px; }

/* ── Animations ──────────────────────────── */
@keyframes slideUp {
    from{opacity:0;transform:translateY(12px)}
    to{opacity:1;transform:translateY(0)}
}
@keyframes fadeSlide {
    from{opacity:0;transform:translateX(-8px)}
    to{opacity:1;transform:translateX(0)}
}
@keyframes pulse {
    0%,100%{opacity:1;transform:scale(1)}
    50%{opacity:.5;transform:scale(1.3)}
}
</style>
@endpush
@endsection
