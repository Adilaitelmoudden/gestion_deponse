@extends('layouts.app')

@section('title', 'Notifications')
@section('header', 'Notifications')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-bell me-2 text-warning"></i> Mes Notifications</h5>
        <span class="badge bg-secondary">{{ $notifications->total() }} au total</span>
    </div>
    <div class="card-body p-0">
        @forelse($notifications as $notif)
        <div class="notif-item d-flex align-items-start gap-3 p-3 border-bottom {{ $notif->is_read ? '' : 'notif-unread' }}">
            <div class="notif-icon flex-shrink-0">
                @if(str_contains($notif->title, '🔴'))
                    <span class="fs-4">🔴</span>
                @elseif(str_contains($notif->title, '🟡'))
                    <span class="fs-4">🟡</span>
                @else
                    <i class="fas fa-info-circle text-primary fs-5"></i>
                @endif
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="notif-title">{{ $notif->title }}</strong>
                    <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-0 text-muted small">{{ $notif->message }}</p>
            </div>
            <form action="{{ route('notifications.destroy', $notif) }}" method="POST" class="flex-shrink-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                    <i class="fas fa-times"></i>
                </button>
            </form>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-bell-slash fa-3x text-muted mb-3 d-block"></i>
            <p class="text-muted">Aucune notification</p>
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
.notif-unread {
    background: color-mix(in srgb, var(--color-input-focus) 6%, transparent);
    border-left: 3px solid var(--color-input-focus) !important;
}
.notif-item { transition: background 0.2s ease; }
.notif-item:hover { background: var(--color-surface-hover); }
</style>
@endpush
@endsection
