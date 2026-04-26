@extends('layouts.app')

@section('title', 'Catégories')
@section('header', 'Gestion des Catégories')

@section('content')

{{-- Stats Bar --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-pill stat-expense">
            <i class="fas fa-arrow-trend-down"></i>
            <div>
                <div class="stat-num">{{ $expenseCategories->count() }}</div>
                <div class="stat-lbl">Catégories Dépenses</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-pill stat-income">
            <i class="fas fa-arrow-trend-up"></i>
            <div>
                <div class="stat-num">{{ $incomeCategories->count() }}</div>
                <div class="stat-lbl">Catégories Revenus</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-pill stat-total">
            <i class="fas fa-layer-group"></i>
            <div>
                <div class="stat-num">{{ $expenseCategories->count() + $incomeCategories->count() }}</div>
                <div class="stat-lbl">Total Catégories</div>
            </div>
        </div>
    </div>
</div>

<div class="card cat-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Mes Catégories</h5>
        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-add">
            <i class="fas fa-plus me-1"></i> Nouvelle Catégorie
        </a>
    </div>

    <div class="card-body">

        {{-- ── DÉPENSES ─────────────────────────────────── --}}
        <div class="section-header expense-header">
            <span class="section-icon"><i class="fas fa-arrow-down"></i></span>
            <h6>Dépenses</h6>
            <span class="section-count">{{ $expenseCategories->count() }}</span>
        </div>

        <div class="row g-3 mb-5" id="expenseGrid">
            @forelse($expenseCategories as $i => $category)
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 cat-col" style="--i:{{ $i }}">
                <div class="cat-card-item">
                    <div class="cat-glow" style="--c:{{ $category->color }}"></div>
                    <div class="cat-icon-wrap" style="background:{{ $category->color }}22; border-color:{{ $category->color }}44">
                        <i class="{{ $category->icon }}" style="color:{{ $category->color }}"></i>
                    </div>
                    <div class="cat-name">{{ $category->name }}</div>
                    <div class="cat-count">
                        <i class="fas fa-receipt"></i>
                        {{ $category->transactions_count ?? 0 }} transaction(s)
                    </div>
                    @if($category->is_default)
                    <span class="cat-badge-default">Par défaut</span>
                    @endif
                    <div class="cat-actions">
                        <a href="{{ route('categories.edit', $category) }}" class="cat-btn cat-btn-edit" title="Modifier">
                            <i class="fas fa-pen"></i>
                        </a>
                        @if(!$category->is_default)
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="cat-btn cat-btn-del" title="Supprimer"
                                onclick="return confirm('Supprimer cette catégorie ?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>Aucune catégorie de dépenses</p>
                    <a href="{{ route('categories.create') }}" class="btn btn-sm btn-outline-danger">Créer une catégorie</a>
                </div>
            </div>
            @endforelse
        </div>

        {{-- ── REVENUS ──────────────────────────────────── --}}
        <div class="section-header income-header">
            <span class="section-icon"><i class="fas fa-arrow-up"></i></span>
            <h6>Revenus</h6>
            <span class="section-count">{{ $incomeCategories->count() }}</span>
        </div>

        <div class="row g-3" id="incomeGrid">
            @forelse($incomeCategories as $i => $category)
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 cat-col" style="--i:{{ $i }}">
                <div class="cat-card-item">
                    <div class="cat-glow" style="--c:{{ $category->color }}"></div>
                    <div class="cat-icon-wrap" style="background:{{ $category->color }}22; border-color:{{ $category->color }}44">
                        <i class="{{ $category->icon }}" style="color:{{ $category->color }}"></i>
                    </div>
                    <div class="cat-name">{{ $category->name }}</div>
                    <div class="cat-count">
                        <i class="fas fa-receipt"></i>
                        {{ $category->transactions_count ?? 0 }} transaction(s)
                    </div>
                    @if($category->is_default)
                    <span class="cat-badge-default">Par défaut</span>
                    @endif
                    <div class="cat-actions">
                        <a href="{{ route('categories.edit', $category) }}" class="cat-btn cat-btn-edit" title="Modifier">
                            <i class="fas fa-pen"></i>
                        </a>
                        @if(!$category->is_default)
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="cat-btn cat-btn-del" title="Supprimer"
                                onclick="return confirm('Supprimer cette catégorie ?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>Aucune catégorie de revenus</p>
                    <a href="{{ route('categories.create') }}" class="btn btn-sm btn-outline-success">Créer une catégorie</a>
                </div>
            </div>
            @endforelse
        </div>

    </div>
</div>

@push('styles')
<style>
/* ── Stats Pills ─────────────────────────── */
.stat-pill {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 20px;
    border-radius: 14px;
    font-size: 22px;
    animation: slideUp .4s ease both;
}
.stat-pill.stat-expense { background: linear-gradient(135deg,#fee2e2,#fecaca); color:#dc2626; }
.stat-pill.stat-income  { background: linear-gradient(135deg,#dcfce7,#bbf7d0); color:#16a34a; }
.stat-pill.stat-total   { background: linear-gradient(135deg,#ede9fe,#ddd6fe); color:#7c3aed; }
.stat-num { font-size: 26px; font-weight: 700; line-height: 1; }
.stat-lbl { font-size: 12px; opacity: .75; margin-top: 2px; }

[data-theme="dark"] .stat-pill.stat-expense { background: linear-gradient(135deg,#450a0a44,#7f1d1d44); }
[data-theme="dark"] .stat-pill.stat-income  { background: linear-gradient(135deg,#052e1644,#14532d44); }
[data-theme="dark"] .stat-pill.stat-total   { background: linear-gradient(135deg,#2e1065 44,#4c1d9544); }

/* ── Section Headers ─────────────────────── */
.section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--color-border);
}
.section-header h6 { margin: 0; font-weight: 700; font-size: 15px; }
.section-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
}
.expense-header .section-icon { background:#fee2e2; color:#dc2626; }
.income-header  .section-icon { background:#dcfce7; color:#16a34a; }
.section-count {
    margin-left: auto;
    background: var(--color-surface-hover);
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 12px;
    font-weight: 600;
    color: var(--color-text-secondary);
}

/* ── Category Cards ──────────────────────── */
.cat-col {
    animation: popIn .35s ease both;
    animation-delay: calc(var(--i) * 50ms);
}
.cat-card-item {
    position: relative;
    overflow: hidden;
    background: var(--color-card-bg);
    border: 1px solid var(--color-card-border);
    border-radius: 16px;
    padding: 20px 14px 14px;
    text-align: center;
    cursor: default;
    transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
}
.cat-card-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,.12);
    border-color: transparent;
}
.cat-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 50% 0%, color-mix(in srgb, var(--c) 18%, transparent), transparent 70%);
    opacity: 0;
    transition: opacity .3s ease;
    pointer-events: none;
}
.cat-card-item:hover .cat-glow { opacity: 1; }

.cat-icon-wrap {
    width: 52px; height: 52px;
    border-radius: 14px;
    border: 2px solid;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 10px;
    font-size: 20px;
    transition: transform .25s ease;
}
.cat-card-item:hover .cat-icon-wrap { transform: scale(1.12) rotate(-4deg); }

.cat-name {
    font-weight: 600;
    font-size: 13px;
    color: var(--color-text-primary);
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cat-count {
    font-size: 11px;
    color: var(--color-text-muted);
    margin-bottom: 8px;
}
.cat-count i { margin-right: 3px; }

.cat-badge-default {
    display: inline-block;
    background: #fef3c7;
    color: #92400e;
    font-size: 10px;
    padding: 1px 7px;
    border-radius: 10px;
    margin-bottom: 8px;
    font-weight: 600;
}
[data-theme="dark"] .cat-badge-default { background:#451a0344; color:#fcd34d; }

.cat-actions {
    display: flex;
    justify-content: center;
    gap: 6px;
    opacity: 0;
    transform: translateY(6px);
    transition: opacity .2s ease, transform .2s ease;
}
.cat-card-item:hover .cat-actions { opacity: 1; transform: translateY(0); }

.cat-btn {
    width: 30px; height: 30px;
    border-radius: 8px;
    border: none;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px;
    cursor: pointer;
    transition: transform .15s ease;
}
.cat-btn:hover { transform: scale(1.15); }
.cat-btn-edit { background:#fef9c3; color:#854d0e; }
.cat-btn-del  { background:#fee2e2; color:#dc2626; }
[data-theme="dark"] .cat-btn-edit { background:#451a0344; color:#fcd34d; }
[data-theme="dark"] .cat-btn-del  { background:#450a0a44; color:#f87171; }

/* ── Empty State ─────────────────────────── */
.empty-state {
    text-align: center;
    padding: 40px;
    color: var(--color-text-muted);
}
.empty-state i { font-size: 36px; margin-bottom: 10px; display: block; }

/* ── Add Button ──────────────────────────── */
.btn-add {
    border-radius: 10px;
    font-size: 13px;
    padding: 7px 16px;
    transition: transform .2s ease, box-shadow .2s ease;
}
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(79,70,229,.35); }

/* ── Animations ──────────────────────────── */
@keyframes popIn {
    from { opacity:0; transform:scale(.88) translateY(10px); }
    to   { opacity:1; transform:scale(1)   translateY(0);    }
}
@keyframes slideUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0);    }
}
</style>
@endpush
@endsection
