<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion des Dépenses')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ══════════════════════════════════════════════════
           DESIGN TOKENS — Light & Dark themes
        ══════════════════════════════════════════════════ */
        :root {
            --color-page-bg:        #f0f2f8;
            --color-surface:        #ffffff;
            --color-surface-hover:  #f8f9fc;
            --color-border:         #e2e8f0;
            --color-text-primary:   #1e2433;
            --color-text-secondary: #64748b;
            --color-text-muted:     #94a3b8;

            --color-sidebar-from:   #4f46e5;
            --color-sidebar-to:     #7c3aed;
            --color-sidebar-link:   rgba(255,255,255,0.80);
            --color-sidebar-hover:  rgba(255,255,255,0.15);
            --color-sidebar-active: rgba(255,255,255,0.25);

            --color-navbar-bg:      #ffffff;
            --color-navbar-border:  #e2e8f0;
            --color-navbar-text:    #1e2433;

            --color-card-bg:        #ffffff;
            --color-card-border:    #e2e8f0;
            --color-card-header-bg: #f8f9fc;

            --color-table-header:   #f1f5f9;
            --color-table-row-hover:#f8faff;
            --color-table-border:   #e2e8f0;

            --color-input-bg:       #ffffff;
            --color-input-border:   #cbd5e1;
            --color-input-focus:    #4f46e5;
            --color-input-text:     #1e2433;

            --color-scrollbar-track:#f1f5f9;
            --color-scrollbar-thumb:#c7d2e0;

            --color-footer-bg:      #ffffff;
            --color-footer-border:  #e2e8f0;

            --color-dropdown-bg:    #ffffff;
            --color-dropdown-hover: #f1f5f9;
            --color-dropdown-text:  #1e2433;

            --shadow-sm:  0 1px 3px rgba(0,0,0,0.07), 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md:  0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg:  0 10px 30px rgba(0,0,0,0.10);

            --speed: 300ms;
        }

        [data-theme="dark"] {
            --color-page-bg:        #0f1117;
            --color-surface:        #1a1d27;
            --color-surface-hover:  #1f2335;
            --color-border:         #2a2d3e;
            --color-text-primary:   #e2e8f0;
            --color-text-secondary: #94a3b8;
            --color-text-muted:     #64748b;

            --color-sidebar-from:   #312e81;
            --color-sidebar-to:     #4c1d95;
            --color-sidebar-link:   rgba(255,255,255,0.75);
            --color-sidebar-hover:  rgba(255,255,255,0.10);
            --color-sidebar-active: rgba(255,255,255,0.18);

            --color-navbar-bg:      #1a1d27;
            --color-navbar-border:  #2a2d3e;
            --color-navbar-text:    #e2e8f0;

            --color-card-bg:        #1a1d27;
            --color-card-border:    #2a2d3e;
            --color-card-header-bg: #1f2335;

            --color-table-header:   #1f2335;
            --color-table-row-hover:#1f2440;
            --color-table-border:   #2a2d3e;

            --color-input-bg:       #0f1117;
            --color-input-border:   #2a2d3e;
            --color-input-focus:    #818cf8;
            --color-input-text:     #e2e8f0;

            --color-scrollbar-track:#1a1d27;
            --color-scrollbar-thumb:#2a2d3e;

            --color-footer-bg:      #1a1d27;
            --color-footer-border:  #2a2d3e;

            --color-dropdown-bg:    #1f2335;
            --color-dropdown-hover: #2a2d3e;
            --color-dropdown-text:  #e2e8f0;

            --shadow-sm:  0 1px 3px rgba(0,0,0,0.3);
            --shadow-md:  0 4px 12px rgba(0,0,0,0.4);
            --shadow-lg:  0 10px 30px rgba(0,0,0,0.5);
        }

        /* ── Global Reset ── */
        *, *::before, *::after { box-sizing: border-box; }

        html {
            transition: background-color var(--speed) ease, color var(--speed) ease;
        }

        body {
            background-color: var(--color-page-bg);
            color: var(--color-text-primary);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            margin: 0;
            transition: background-color var(--speed) ease, color var(--speed) ease;
        }

        /* ── Sidebar ── */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(160deg, var(--color-sidebar-from) 0%, var(--color-sidebar-to) 100%);
            position: fixed;
            left: 0; top: 0;
            width: 270px;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            transition: all var(--speed) ease, background var(--speed) ease;
        }

        .sidebar-logo-wrapper {
            padding: 2rem 1.5rem 1rem;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-logo-icon {
            width: 60px; height: 60px;
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
            backdrop-filter: blur(8px);
            transition: transform 200ms ease, background 200ms ease;
        }

        .sidebar-logo-icon:hover {
            transform: scale(1.08);
            background: rgba(255,255,255,0.25);
        }

        .sidebar-title {
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            margin: 0 0 0.2rem;
        }

        .sidebar-subtitle {
            color: rgba(255,255,255,0.55);
            font-size: 0.73rem;
            margin: 0;
        }

        .sidebar-divider {
            border-color: rgba(255,255,255,0.15);
            margin: 0.75rem 1.25rem;
        }

        .sidebar-nav {
            flex: 1;
            padding: 0 0.75rem;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: var(--color-sidebar-link);
            padding: 0.7rem 1rem;
            margin-bottom: 2px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
            transition: background var(--speed) ease, color 200ms ease, transform 200ms ease;
        }

        .sidebar .nav-link:hover {
            background: var(--color-sidebar-hover);
            color: #fff;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: var(--color-sidebar-active);
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }

        .sidebar .nav-link i { width: 20px; font-size: 1rem; text-align: center; flex-shrink: 0; }

        .sidebar-footer { padding: 1rem 1.5rem; text-align: center; }

        /* ── Main Content ── */
        .main-content {
            margin-left: 270px;
            transition: margin-left var(--speed) ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top Navbar ── */
        .top-navbar {
            background: var(--color-navbar-bg);
            padding: 0.85rem 1.75rem;
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--color-navbar-border);
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background-color var(--speed) ease, border-color var(--speed) ease;
        }

        .top-navbar .page-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--color-navbar-text);
            margin: 0;
            transition: color var(--speed) ease;
        }

        /* ── Dark Mode Toggle Button ── */
        .dark-mode-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 1rem;
            border-radius: 999px;
            border: 1.5px solid var(--color-border);
            background: var(--color-surface);
            color: var(--color-text-secondary);
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
            transition:
                background-color var(--speed) ease,
                border-color 200ms ease,
                color 200ms ease,
                transform 150ms ease,
                box-shadow 150ms ease;
        }

        .dark-mode-toggle-btn:hover {
            border-color: #4f46e5;
            color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
            transform: translateY(-1px);
        }

        [data-theme="dark"] .dark-mode-toggle-btn:hover {
            border-color: #818cf8;
            color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129,140,248,0.15);
        }

        .dark-mode-toggle-btn:active {
            transform: translateY(0) scale(0.97);
        }

        .dark-mode-toggle-btn .toggle-icon {
            font-size: 0.95rem;
            line-height: 1;
            display: inline-block;
            transition: transform 400ms cubic-bezier(0.34,1.56,0.64,1);
        }

        [data-theme="dark"] .dark-mode-toggle-btn .toggle-icon {
            transform: rotate(20deg);
        }

        /* ── Cards ── */
        .card {
            background: var(--color-card-bg);
            border: 1px solid var(--color-card-border);
            border-radius: 14px;
            box-shadow: var(--shadow-sm);
            transition:
                background-color var(--speed) ease,
                border-color var(--speed) ease,
                box-shadow 200ms ease,
                transform 200ms ease;
        }

        .card:hover { box-shadow: var(--shadow-md); }

        .card-header {
            background: var(--color-card-header-bg);
            border-bottom: 1px solid var(--color-card-border);
            border-radius: 14px 14px 0 0 !important;
            padding: 1rem 1.25rem;
            transition: background-color var(--speed) ease, border-color var(--speed) ease;
        }

        .card-header h5 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--color-text-primary);
            transition: color var(--speed) ease;
        }

        .card-body {
            padding: 1.25rem;
            color: var(--color-text-primary);
            transition: color var(--speed) ease;
        }

        .card-stats {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            transition: transform 250ms ease, box-shadow 250ms ease;
        }

        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        /* ── Tables ── */
        .table-responsive { border-radius: 12px; overflow: hidden; }

        .table {
            --bs-table-bg: var(--color-card-bg);
            --bs-table-hover-bg: var(--color-table-row-hover);
            --bs-table-border-color: var(--color-table-border);
            color: var(--color-text-primary);
            margin-bottom: 0;
            transition: color var(--speed) ease;
        }

        .table thead th {
            background: var(--color-table-header);
            border-bottom: 2px solid var(--color-table-border);
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-text-secondary);
            padding: 0.85rem 1rem;
            transition: background-color var(--speed) ease, color var(--speed) ease, border-color var(--speed) ease;
        }

        .table tbody tr {
            transition: background-color 150ms ease, transform 150ms ease;
        }

        .table tbody tr:hover {
            background-color: var(--color-table-row-hover);
        }

        .table td {
            padding: 0.8rem 1rem;
            vertical-align: middle;
            border-color: var(--color-table-border);
            transition: background-color var(--speed) ease, border-color var(--speed) ease;
        }

        /* ── Forms ── */
        .form-control, .form-select {
            background-color: var(--color-input-bg);
            border-color: var(--color-input-border);
            color: var(--color-input-text);
            border-radius: 8px;
            transition:
                background-color var(--speed) ease,
                border-color 200ms ease,
                color var(--speed) ease,
                box-shadow 200ms ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--color-input-bg);
            border-color: var(--color-input-focus);
            color: var(--color-input-text);
            box-shadow: 0 0 0 3px rgba(79,70,229,0.15);
        }

        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus {
            box-shadow: 0 0 0 3px rgba(129,140,248,0.2);
        }

        .form-control::placeholder { color: var(--color-text-muted); }

        .form-label {
            color: var(--color-text-secondary);
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
            transition: color var(--speed) ease;
        }

        /* ── Buttons ── */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            transition:
                transform 150ms ease,
                box-shadow 150ms ease,
                background-color 200ms ease,
                border-color 200ms ease;
        }

        .btn:hover { transform: translateY(-1px); box-shadow: var(--shadow-md); }
        .btn:active { transform: translateY(0) scale(0.98); }

        .btn-action { padding: 0.35rem 0.75rem; margin: 0 2px; border-radius: 8px; font-size: 0.8rem; }

        /* ── Dropdown ── */
        .dropdown-menu {
            background-color: var(--color-dropdown-bg);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            padding: 0.4rem;
            transition: background-color var(--speed) ease, border-color var(--speed) ease;
        }

        .dropdown-item {
            color: var(--color-dropdown-text);
            border-radius: 8px;
            padding: 0.55rem 0.9rem;
            font-size: 0.88rem;
            transition: background-color 150ms ease, color 150ms ease, transform 150ms ease;
        }

        .dropdown-item:hover {
            background-color: var(--color-dropdown-hover);
            color: var(--color-text-primary);
            transform: translateX(3px);
        }

        .dropdown-divider { border-color: var(--color-border); margin: 0.35rem 0; }

        /* ── Alerts ── */
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: var(--shadow-sm);
            animation: slideDownFade 0.35s ease both;
        }

        /* ── Progress ── */
        .progress {
            height: 8px;
            border-radius: 10px;
            background: var(--color-border);
            transition: background-color var(--speed) ease;
        }

        /* ── Badges ── */
        .badge-category {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-block;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: var(--color-scrollbar-track); }
        ::-webkit-scrollbar-thumb { background: var(--color-scrollbar-thumb); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--color-text-muted); }

        /* ── Footer ── */
        .footer {
            background: var(--color-footer-bg);
            padding: 1rem 1.75rem;
            border-top: 1px solid var(--color-footer-border);
            margin-top: auto;
            transition: background-color var(--speed) ease, border-color var(--speed) ease;
        }

        .footer small { color: var(--color-text-muted); transition: color var(--speed) ease; }

        /* ── Animations ── */
        @keyframes fadeUpIn {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDownFade {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .main-page-content { animation: fadeUpIn 0.4s ease both; }

        /* ── Misc ── */
        .text-muted { color: var(--color-text-muted) !important; }
        hr { border-color: var(--color-border); transition: border-color var(--speed) ease; }

        /* Prevent white flash on dark mode reload */
        html[data-theme="dark"] { background-color: #0f1117; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.sidebar-open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .top-navbar { padding: 0.75rem 1rem; }
        }

        /* ── Notification Bell ── */
        .nav-notif-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--color-surface);
            border: 1.5px solid var(--color-border);
            color: var(--color-text-secondary);
            text-decoration: none;
            transition: all 200ms ease;
        }
        .nav-notif-btn:hover {
            color: var(--color-input-focus);
            border-color: var(--color-input-focus);
            background: color-mix(in srgb, var(--color-input-focus) 10%, var(--color-surface));
            transform: translateY(-1px);
        }
        .notif-badge {
            position: absolute;
            top: -5px; right: -5px;
            min-width: 18px; height: 18px;
            background: #ef4444;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid var(--color-navbar-bg);
            animation: badgePop 0.3s ease;
        }
        @keyframes badgePop {
            0%   { transform: scale(0); }
            70%  { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* ── Card entrance animation ── */
        .card { animation: fadeUp 0.3s ease both; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Table row hover polish ── */
        .table tbody tr { transition: background 0.15s ease; }

        /* ── Stat cards pulse on load ── */
        .card-stats { animation: fadeUp 0.4s ease both; }

        /* ── Button hover lift ── */
        .btn { transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease; }
        .btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .btn:active { transform: translateY(0); }

        /* ── Smooth sidebar link transitions ── */
        .sidebar .nav-link { transition: background 0.2s ease, color 0.2s ease, padding-left 0.2s ease; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { padding-left: 1.3rem; }

        /* ── Alert entrance ── */
        .alert { animation: slideDown 0.3s ease; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('styles')

    {{-- Anti-flash: apply saved theme before paint --}}
    <script>
        (function () {
            var saved = localStorage.getItem('app-theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        }());
    </script>
</head>
<body>

<div class="d-flex">

    {{-- ══ SIDEBAR ══════════════════════════════════════════ --}}
    <aside class="sidebar" id="appSidebar">

        <div class="sidebar-logo-wrapper">
            <div class="sidebar-logo-icon">
                <i class="fas fa-coins fa-2x text-white"></i>
            </div>
            <h2 class="sidebar-title">Gestion Dépenses</h2>
            <p class="sidebar-subtitle">Gérez vos finances facilement</p>
            <hr class="sidebar-divider">
        </div>

        <nav class="sidebar-nav nav flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
               href="{{ route('transactions.index') }}">
                <i class="fas fa-exchange-alt"></i> Transactions
            </a>
            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
               href="{{ route('categories.index') }}">
                <i class="fas fa-tags"></i> Catégories
            </a>
            <a class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}"
               href="{{ route('budgets.index') }}">
                <i class="fas fa-chart-pie"></i> Budgets
            </a>
            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
               href="{{ route('reports.index') }}">
                <i class="fas fa-file-alt"></i> Rapports
            </a>
            <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }} d-flex align-items-center justify-content-between"
               href="{{ route('notifications.index') }}">
                <span><i class="fas fa-bell"></i> Notifications</span>
                <span class="badge bg-danger rounded-pill sidebar-notif-badge" id="sidebarNotifBadge" style="display:none">0</span>
            </a>

            @if(session('user_role') == 'admin')
                <hr class="sidebar-divider">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                   href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users-cog"></i> Gestion Users
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <hr class="sidebar-divider">
            <small style="color: rgba(255,255,255,0.45);">
                <i class="fas fa-code-branch me-1"></i> Version 1.0
            </small>
        </div>
    </aside>

    {{-- ══ MAIN CONTENT ═══════════════════════════════════════ --}}
    <div class="main-content flex-grow-1" id="mainContentArea">

        {{-- ── TOP NAVBAR ──────────────────────────────────────── --}}
        <nav class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link p-0 d-md-none text-muted"
                        id="mobileSidebarToggle"
                        aria-label="Ouvrir le menu">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <h1 class="page-title">@yield('header', 'Tableau de Bord')</h1>
            </div>

            <div class="d-flex align-items-center gap-3">

                {{-- Date --}}
                <div class="d-none d-sm-block"
                     style="color: var(--color-text-muted); font-size: 0.82rem;">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ now()->format('l d/m/Y') }}
                </div>

                {{-- ★ NOTIFICATION BELL ★ --}}
                <a href="{{ route('notifications.index') }}" class="nav-notif-btn" id="notifBellBtn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notif-badge" id="notifBadgeTop" style="display:none">0</span>
                </a>

                {{-- ★ DARK MODE TOGGLE ★ --}}
                <button id="darkModeToggleBtn"
                        class="dark-mode-toggle-btn"
                        aria-label="Basculer le thème"
                        aria-pressed="false">
                    <span class="toggle-icon" id="darkModeToggleIcon">🌙</span>
                    <span class="d-none d-sm-inline" id="darkModeToggleLabel">Mode sombre</span>
                </button>

                {{-- User Dropdown --}}
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center gap-2"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            style="background: var(--color-surface);
                                   border: 1.5px solid var(--color-border);
                                   color: var(--color-text-primary);">
                        <i class="fas fa-user-circle fa-lg text-primary"></i>
                        <span class="fw-bold d-none d-sm-inline">{{ session('user_name') }}</span>
                        @if(session('user_role') == 'admin')
                            <span class="badge bg-danger">Admin</span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fas fa-user me-2 text-primary"></i> Mon Profil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('notifications.index') }}">
                                <i class="fas fa-bell me-2 text-warning"></i> Notifications
                                <span class="badge bg-danger rounded-pill ms-1" id="notifBadgeDropdown">0</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>

        {{-- ── PAGE CONTENT ─────────────────────────────────────── --}}
        <main class="main-page-content py-4 px-4 flex-grow-1">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Erreurs de validation :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            @yield('content')
        </main>

        {{-- ── FOOTER ───────────────────────────────────────────── --}}
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 text-md-start text-center">
                        <small>&copy; {{ date('Y') }} Gestion des Dépenses. Tous droits réservés.</small>
                    </div>
                    <div class="col-md-6 text-md-end text-center">
                        <small>
                            <i class="fas fa-heart text-danger"></i> Développé avec Laravel
                        </small>
                    </div>
                </div>
            </div>
        </footer>

    </div>{{-- /main-content --}}
</div>{{-- /d-flex --}}

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ══════════════════════════════════════════════════════════
   DARK MODE MANAGER
   Persists user preference in localStorage ('app-theme').
   Applies theme via data-theme attribute on <html>.
══════════════════════════════════════════════════════════ */
(function () {
    var STORAGE_KEY     = 'app-theme';
    var htmlElement     = document.documentElement;
    var toggleButton    = document.getElementById('darkModeToggleBtn');
    var toggleIcon      = document.getElementById('darkModeToggleIcon');
    var toggleLabel     = document.getElementById('darkModeToggleLabel');

    /** Update the <html> attribute and the toggle button visuals */
    function applyTheme(theme) {
        htmlElement.setAttribute('data-theme', theme);

        if (theme === 'dark') {
            if (toggleIcon)  toggleIcon.textContent  = '☀️';
            if (toggleLabel) toggleLabel.textContent = 'Mode clair';
            if (toggleButton) {
                toggleButton.setAttribute('aria-pressed', 'true');
                toggleButton.setAttribute('aria-label', 'Activer le mode clair');
            }
        } else {
            if (toggleIcon)  toggleIcon.textContent  = '🌙';
            if (toggleLabel) toggleLabel.textContent = 'Mode sombre';
            if (toggleButton) {
                toggleButton.setAttribute('aria-pressed', 'false');
                toggleButton.setAttribute('aria-label', 'Activer le mode sombre');
            }
        }
    }

    /** Flip dark ↔ light and save the choice */
    function toggleTheme() {
        var current = htmlElement.getAttribute('data-theme') || 'light';
        var next    = (current === 'dark') ? 'light' : 'dark';
        localStorage.setItem(STORAGE_KEY, next);
        applyTheme(next);
    }

    /** Read saved preference (or OS default) and apply */
    function bootTheme() {
        var saved = localStorage.getItem(STORAGE_KEY);
        if (saved === 'dark' || saved === 'light') {
            applyTheme(saved);
        } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            applyTheme('dark');
        } else {
            applyTheme('light');
        }
    }

    if (toggleButton) {
        toggleButton.addEventListener('click', toggleTheme);
    }

    /* Sync button labels once the DOM is ready */
    document.addEventListener('DOMContentLoaded', bootTheme);
}());


/* ══════════════════════════════════════════════════════════
   MOBILE SIDEBAR TOGGLE
══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {

    var mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    var appSidebar          = document.getElementById('appSidebar');

    if (mobileSidebarToggle && appSidebar) {
        mobileSidebarToggle.addEventListener('click', function () {
            appSidebar.classList.toggle('sidebar-open');
        });
    }

    /* Auto-dismiss flash alerts after 5 seconds */
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (alertEl) {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
            if (bsAlert) {
                setTimeout(function () {
                    try { bsAlert.close(); } catch (e) {}
                }, 5000);
            }
        });
    }, 500);

});
</script>

<script>
/* ── Live notification badge ── */
(function pollNotifications() {
    function update() {
        fetch('{{ route("notifications.unread-count") }}')
            .then(r => r.json())
            .then(data => {
                var count = data.count || 0;
                var badgeTop = document.getElementById('notifBadgeTop');
                var badgeDrop = document.getElementById('notifBadgeDropdown');
                if (badgeTop) {
                    badgeTop.textContent = count > 99 ? '99+' : count;
                    badgeTop.style.display = count > 0 ? 'inline-flex' : 'none';
                }
                var sidebarBadge = document.getElementById('sidebarNotifBadge');
                if (sidebarBadge) {
                    sidebarBadge.textContent = count > 99 ? '99+' : count;
                    sidebarBadge.style.display = count > 0 ? 'inline-flex' : 'none';
                }
                if (badgeDrop) {
                    badgeDrop.textContent = count > 99 ? '99+' : count;
                    badgeDrop.style.display = count > 0 ? 'inline-flex' : 'none';
                }
            })
            .catch(() => {});
    }
    update();
    setInterval(update, 30000); // refresh every 30s
}());
</script>



@stack('scripts')
</body>
</html>
