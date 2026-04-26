@extends('layouts.app')

@section('title', 'Historique des Notifications')
@section('header', 'Historique des Notifications')

@section('content')
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Historique</h5>
        <a href="{{ route('admin.notifications.compose') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Nouvelle notification
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Utilisateur</label>
                <select name="user_id" class="form-select">
                    <option value="">Tous</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut de lecture</label>
                <select name="is_read" class="form-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Lues</option>
                    <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Non lues</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-filter me-1"></i>Filtrer
                </button>
                <a href="{{ route('admin.notifications.history') }}" class="btn btn-outline-secondary ms-1">
                    <i class="fas fa-times me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Destinataire</th>
                        <th>Titre</th>
                        <th>Message</th>
                        <th>Lu</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($notifications as $n)
                    <tr>
                        <td style="color:var(--color-text-muted)">{{ $n->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $n->user?->name ?? '-' }}</div>
                            <small style="color:var(--color-text-muted)">{{ $n->user?->email }}</small>
                        </td>
                        <td class="fw-semibold">{{ $n->title }}</td>
                        <td style="max-width:220px">
                            <span title="{{ $n->message }}" style="color:var(--color-text-muted)">
                                {{ Str::limit($n->message, 60) }}
                            </span>
                        </td>
                        <td>
                            @if($n->is_read)
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Lu</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="fas fa-circle me-1"></i>Non lu</span>
                            @endif
                        </td>
                        <td style="font-size:.82rem;color:var(--color-text-muted)">{{ $n->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <form action="{{ route('admin.notifications.destroy', $n) }}" method="POST"
                                  onsubmit="return confirm('Supprimer cette notification ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color:var(--color-text-muted)">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucune notification trouvée.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($notifications->hasPages())
    <div class="card-body border-top" style="border-color:var(--color-border)!important">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
