<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — TDR HPZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --adm-bg:        #0b0b0f;
            --adm-sidebar:   #111118;
            --adm-surface:   rgba(26,26,46,0.5);
            --adm-red:       #e63946;
            --adm-red-h:     #ff4d5a;
            --adm-gold:      #d4a843;
            --adm-text:      #e8e8e8;
            --adm-muted:     #6b7084;
            --adm-border:    rgba(255,255,255,0.07);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--adm-bg);
            color: var(--adm-text);
            margin: 0;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Sidebar ─────────────────────────────────────────────── */
        .adm-sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--adm-sidebar);
            border-right: 1px solid var(--adm-border);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .adm-sidebar .brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .adm-sidebar .brand-icon {
            background: var(--adm-red);
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.9rem;
        }
        .adm-sidebar .brand-text {
            font-weight: 800;
            font-size: 1rem;
            color: #fff;
            letter-spacing: -0.3px;
        }
        .adm-sidebar .brand-sub {
            font-size: 0.65rem;
            color: var(--adm-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .adm-sidebar .nav-section {
            padding: 16px 12px;
            flex: 1;
        }
        .adm-sidebar .nav-label {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--adm-muted);
            padding: 0 12px;
            margin-bottom: 8px;
            margin-top: 16px;
        }
        .adm-sidebar .nav-label:first-child { margin-top: 0; }
        .adm-sidebar a.nav-link {
            color: var(--adm-muted);
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
            margin-bottom: 2px;
        }
        .adm-sidebar a.nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        .adm-sidebar a.nav-link:hover {
            background: rgba(255,255,255,0.04);
            color: var(--adm-text);
        }
        .adm-sidebar a.nav-link.active {
            background: rgba(230,57,70,0.1);
            color: var(--adm-red);
        }
        .adm-sidebar a.nav-link.active i { color: var(--adm-red); }

        .adm-sidebar .sidebar-footer {
            padding: 12px;
            border-top: 1px solid var(--adm-border);
        }
        .adm-sidebar .sidebar-footer .btn-link {
            color: var(--adm-muted);
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            width: 100%;
            text-align: left;
            border: none;
            background: none;
            transition: all 0.15s ease;
        }
        .adm-sidebar .sidebar-footer .btn-link:hover {
            background: rgba(230,57,70,0.08);
            color: var(--adm-red);
        }

        /* ── Main Content ────────────────────────────────────────── */
        .adm-main {
            margin-left: 240px;
            min-height: 100vh;
            padding: 24px 32px;
        }

        /* ── Cards ───────────────────────────────────────────────── */
        .card {
            background: var(--adm-surface);
            backdrop-filter: blur(12px);
            border: 1px solid var(--adm-border);
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.15);
            color: var(--adm-text);
        }
        .card-header {
            background: rgba(255,255,255,0.025);
            border-bottom: 1px solid var(--adm-border);
            padding: 14px 20px;
            color: var(--adm-text);
        }
        .card-footer {
            background: rgba(255,255,255,0.02);
            border-top: 1px solid var(--adm-border);
        }

        /* ── Stat Cards ──────────────────────────────────────────── */
        .stat-card {
            border-left: 3px solid;
            border-radius: 12px;
            padding: 20px !important;
        }
        .stat-card.blue   { border-left-color: #3b82f6; }
        .stat-card.green  { border-left-color: #10b981; }
        .stat-card.orange { border-left-color: #f59e0b; }
        .stat-card.red    { border-left-color: var(--adm-red); }
        .stat-card .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--adm-muted);
            margin-bottom: 4px;
        }
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--adm-text);
        }

        /* ── Forms ───────────────────────────────────────────────── */
        .form-control, .form-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--adm-border);
            color: var(--adm-text);
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.07);
            border-color: var(--adm-red);
            color: var(--adm-text);
            box-shadow: 0 0 0 3px rgba(230,57,70,0.12);
        }
        .form-control::placeholder { color: var(--adm-muted); }
        .form-label { color: var(--adm-text); font-weight: 600; font-size: 0.85rem; }
        .form-select option,
        .form-select optgroup {
            background: #111118;
            color: var(--adm-text);
        }
        .form-select option:checked {
            background: var(--adm-red);
            color: #fff;
        }

        /* ── Tables ──────────────────────────────────────────────── */
        .table { color: var(--adm-text) !important; --bs-table-bg: transparent; --bs-table-color: var(--adm-text); }
        .table thead th {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--adm-muted) !important;
            padding: 12px 16px;
            border: none;
            border-bottom: 1px solid var(--adm-border);
        }
        .table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--adm-border);
            vertical-align: middle;
            font-size: 0.875rem;
            color: var(--adm-text) !important;
        }
        .table tfoot td, .table tfoot th {
            color: var(--adm-text) !important;
        }
        .table-hover tbody tr:hover {
            background: rgba(255,255,255,0.025);
            --bs-table-hover-bg: rgba(255,255,255,0.025);
            --bs-table-hover-color: var(--adm-text);
        }
        .table-light { --bs-table-bg: rgba(255,255,255,0.025); --bs-table-color: var(--adm-text); }

        /* ── Buttons ─────────────────────────────────────────────── */
        .btn-primary {
            background: var(--adm-red);
            border: none;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: var(--adm-red-h);
            transform: translateY(-1px);
        }
        .btn-warning {
            background: var(--adm-gold);
            border: none;
            color: #0b0b0f;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-outline-primary {
            color: var(--adm-red);
            border-color: var(--adm-red);
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-outline-primary:hover {
            background: var(--adm-red);
            color: #fff;
        }
        .btn-outline-secondary {
            color: var(--adm-muted);
            border-color: var(--adm-border);
            border-radius: 8px;
        }
        .btn-outline-secondary:hover {
            background: rgba(255,255,255,0.05);
            color: var(--adm-text);
            border-color: rgba(255,255,255,0.12);
        }
        .btn-success { background: #10b981; border: none; border-radius: 8px; font-weight: 600; }
        .btn-danger  { background: var(--adm-red); border: none; border-radius: 8px; font-weight: 600; }

        /* ── Alerts ──────────────────────────────────────────────── */
        .alert { border-radius: 10px; font-size: 0.875rem; border: 1px solid; }
        .alert-success { background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.2); color: #5dd39e; }
        .alert-danger  { background: rgba(230,57,70,0.1); border-color: rgba(230,57,70,0.2); color: #ff6b7a; }
        .alert-warning { background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.2); color: #fbbf24; }
        .alert-info    { background: rgba(59,130,246,0.1); border-color: rgba(59,130,246,0.2); color: #60a5fa; }
        .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

        /* ── Badges ──────────────────────────────────────────────── */
        .badge { font-weight: 600; font-size: 0.72rem; border-radius: 6px; letter-spacing: 0.3px; }
        .badge-pending    { background: rgba(245,158,11,0.15) !important; color: #fbbf24 !important; }
        .badge-processing { background: rgba(139,92,246,0.15) !important; color: #a78bfa !important; }
        .badge-shipped    { background: rgba(6,182,212,0.15) !important; color: #22d3ee !important; }
        .badge-delivered  { background: rgba(16,185,129,0.15) !important; color: #34d399 !important; }
        .badge-cancelled  { background: rgba(107,114,128,0.15) !important; color: #9ca3af !important; }
        .badge-paid       { background: rgba(59,130,246,0.15) !important; color: #60a5fa !important; }
        .badge-failed     { background: rgba(230,57,70,0.15) !important; color: #ff6b7a !important; }

        /* ── Pagination ──────────────────────────────────────────── */
        .pagination .page-link {
            background: rgba(255,255,255,0.04);
            border-color: var(--adm-border);
            color: var(--adm-muted);
            font-size: 0.82rem;
        }
        .pagination .page-item.active .page-link {
            background: var(--adm-red);
            border-color: var(--adm-red);
            color: #fff;
        }

        /* ── List Groups ─────────────────────────────────────────── */
        .list-group-item {
            background: transparent;
            border-color: var(--adm-border);
            color: var(--adm-text);
        }

        /* ── Misc ────────────────────────────────────────────────── */
        .text-muted { color: var(--adm-muted) !important; }
        .text-danger { color: var(--adm-red) !important; }
        a { color: var(--adm-red); }
        a:hover { color: var(--adm-red-h); }
        code { color: var(--adm-gold); background: rgba(212,168,67,0.08); padding: 2px 6px; border-radius: 4px; font-size: 0.85em; }
        hr { border-color: var(--adm-border); opacity: 1; }

        /* ── Force light text everywhere (Bootstrap dark override) ── */
        body, h1, h2, h3, h4, h5, h6, p, span, div, label, td, th,
        li, strong, b, em, small, .fw-bold, .fw-semibold,
        .card-body, .card-title, .card-text,
        .list-group-item, .modal-body, .modal-title,
        .dropdown-item, .breadcrumb-item,
        .accordion-button, .offcanvas-body,
        tfoot td, tfoot th, thead td, thead th {
            color: var(--adm-text);
        }
        .text-muted, .card-text.text-muted, small.text-muted { color: var(--adm-muted) !important; }
        .badge { color: inherit; }
        .btn { color: #fff; }
        .btn-outline-secondary { color: var(--adm-muted); }
        .btn-outline-secondary:hover { color: var(--adm-text); }
        .btn-warning, .btn-warning:hover { color: #0b0b0f; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--adm-bg); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <div class="adm-sidebar">
        <div class="brand">
            <div class="brand-icon"><i class="bi bi-gear-fill"></i></div>
            <div>
                <div class="brand-text">TDR HPZ</div>
                <div class="brand-sub">Admin Panel</div>
            </div>
        </div>
        <div class="nav-section">
            <div class="nav-label">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dasbor
            </a>

            <div class="nav-label">Afiliasi</div>
            @php $pendingAff = \App\Models\AffiliateProfile::where('status','pending')->count(); @endphp
            <a href="{{ route('admin.affiliates') }}" class="nav-link {{ request()->routeIs('admin.affiliates') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Afiliasi
                @if($pendingAff > 0)
                    <span class="badge rounded-pill ms-auto" style="background:rgba(245,158,11,0.2);color:#fbbf24;font-size:.68rem">{{ $pendingAff }}</span>
                @endif
            </a>
            @php $pendingWithdrawals = \App\Models\AffiliateWithdrawal::pending()->count(); @endphp
            <a href="{{ route('admin.withdrawals') }}" class="nav-link {{ request()->routeIs('admin.withdrawals*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Penarikan Dana
                @if($pendingWithdrawals > 0)
                    <span class="badge rounded-pill ms-auto" style="background:rgba(245,158,11,0.2);color:#fbbf24;font-size:.68rem">{{ $pendingWithdrawals }}</span>
                @endif
            </a>
            <a href="{{ route('admin.commissions') }}" class="nav-link {{ request()->routeIs('admin.commissions') ? 'active' : '' }}">
                <i class="bi bi-percent"></i> Komisi
            </a>

            <div class="nav-label">Log</div>
            <a href="{{ route('admin.notifications') }}" class="nav-link {{ request()->routeIs('admin.notifications') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Log Notifikasi
            </a>
            <a href="{{ route('admin.audit-log') }}" class="nav-link {{ request()->routeIs('admin.audit-log') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> Audit Log
            </a>

            <div class="nav-label">Transaksi</div>
            @php $pendingOrders = \App\Models\Order::where('status','pending')->count(); @endphp
            <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <i class="bi bi-bag"></i> Pesanan
                @if($pendingOrders > 0)
                    <span class="badge rounded-pill ms-auto" style="background:rgba(245,158,11,0.2);color:#fbbf24;font-size:.68rem">{{ $pendingOrders }}</span>
                @endif
            </a>

            <div class="nav-label">Katalog</div>
            <a href="{{ route('admin.products') }}" class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Produk
            </a>

            <div class="nav-label">Manajemen</div>
            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i> Pengguna
            </a>
        </div>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn-link">
                    <i class="bi bi-box-arrow-left"></i> Keluar
                </button>
            </form>
        </div>
    </div>
    <main class="adm-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-warning mb-3">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
