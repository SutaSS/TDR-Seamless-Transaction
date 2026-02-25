<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TDR HPZ Store - Distributor resmi spare part motor berkualitas tinggi. Harga kompetitif, pengiriman cepat ke seluruh Indonesia.">
    <title>@yield('title', 'TDR HPZ Store')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --tdr-dark:       #0b0b0f;
            --tdr-surface:    #1a1a2e;
            --tdr-surface-2:  #16213e;
            --tdr-red:        #e63946;
            --tdr-red-hover:  #ff4d5a;
            --tdr-gold:       #d4a843;
            --tdr-gold-hover: #e6bc5a;
            --tdr-text:       #e8e8e8;
            --tdr-muted:      #8b8fa3;
            --tdr-border:     rgba(255,255,255,0.08);
            --tdr-card-bg:    rgba(26,26,46,0.6);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--tdr-dark);
            color: var(--tdr-text);
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Navbar ──────────────────────────────────────────────── */
        .tdr-navbar {
            background: rgba(11,11,15,0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--tdr-border);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .tdr-navbar .navbar-brand {
            font-weight: 800;
            font-size: 1.25rem;
            color: #fff !important;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tdr-navbar .navbar-brand .brand-mark {
            background: var(--tdr-red);
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
        }
        .tdr-navbar .nav-link {
            color: var(--tdr-muted) !important;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 1rem 0.875rem !important;
            transition: color 0.2s ease;
            position: relative;
        }
        .tdr-navbar .nav-link:hover,
        .tdr-navbar .nav-link.active {
            color: #fff !important;
        }
        .tdr-navbar .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0.875rem;
            right: 0.875rem;
            height: 2px;
            background: var(--tdr-red);
            border-radius: 2px 2px 0 0;
            transform: scaleX(0);
            transition: transform 0.2s ease;
        }
        .tdr-navbar .nav-link:hover::after,
        .tdr-navbar .nav-link.active::after {
            transform: scaleX(1);
        }
        .tdr-navbar .btn-outline-light {
            border-color: rgba(255,255,255,0.2);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.35rem 1rem;
            transition: all 0.2s ease;
        }
        .tdr-navbar .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.3);
        }
        .tdr-navbar .btn-register {
            background: var(--tdr-red);
            border: none;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.35rem 1.2rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .tdr-navbar .btn-register:hover {
            background: var(--tdr-red-hover);
            color: #fff;
            transform: translateY(-1px);
        }
        .tdr-navbar .user-info {
            color: var(--tdr-muted);
            font-size: 0.825rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .tdr-navbar .user-info:hover { color: #fff; }

        /* ── Cards ───────────────────────────────────────────────── */
        .card {
            background: var(--tdr-card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--tdr-border);
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.2);
            color: var(--tdr-text);
        }
        .card-header {
            background: rgba(255,255,255,0.03);
            border-bottom: 1px solid var(--tdr-border);
            color: var(--tdr-text);
        }
        .card-footer {
            background: rgba(255,255,255,0.02);
            border-top: 1px solid var(--tdr-border);
        }

        /* ── Forms ───────────────────────────────────────────────── */
        .form-control, .form-select {
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--tdr-border);
            color: var(--tdr-text);
            border-radius: 8px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.08);
            border-color: var(--tdr-red);
            color: var(--tdr-text);
            box-shadow: 0 0 0 3px rgba(230,57,70,0.15);
        }
        .form-control::placeholder { color: var(--tdr-muted); }
        .form-label { color: var(--tdr-text); font-weight: 600; font-size: 0.875rem; }
        .form-text { color: var(--tdr-muted); font-size: 0.8rem; }
        .form-check-input {
            background-color: rgba(255,255,255,0.1);
            border-color: var(--tdr-border);
        }
        .form-check-input:checked {
            background-color: var(--tdr-red);
            border-color: var(--tdr-red);
        }
        .form-select option,
        .form-select optgroup {
            background: #1a1a2e;
            color: var(--tdr-text);
        }
        .form-select option:checked {
            background: var(--tdr-red);
            color: #fff;
        }
        .invalid-feedback { font-size: 0.8rem; }

        /* ── Buttons ─────────────────────────────────────────────── */
        .btn-primary {
            background: var(--tdr-red);
            border: none;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: var(--tdr-red-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(230,57,70,0.3);
        }
        .btn-warning {
            background: var(--tdr-gold);
            border: none;
            color: #0b0b0f;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-warning:hover {
            background: var(--tdr-gold-hover);
            color: #0b0b0f;
            transform: translateY(-1px);
        }
        .btn-outline-primary {
            color: var(--tdr-red);
            border-color: var(--tdr-red);
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-outline-primary:hover {
            background: var(--tdr-red);
            border-color: var(--tdr-red);
            color: #fff;
        }
        .btn-outline-secondary {
            color: var(--tdr-muted);
            border-color: var(--tdr-border);
            border-radius: 8px;
        }
        .btn-outline-secondary:hover {
            background: rgba(255,255,255,0.06);
            color: var(--tdr-text);
            border-color: rgba(255,255,255,0.15);
        }
        .btn-success {
            background: #198754;
            border: none;
            border-radius: 8px;
            font-weight: 600;
        }

        /* ── Alerts ──────────────────────────────────────────────── */
        .alert {
            border-radius: 10px;
            font-size: 0.9rem;
            border: 1px solid;
        }
        .alert-success {
            background: rgba(25,135,84,0.12);
            border-color: rgba(25,135,84,0.25);
            color: #5dd39e;
        }
        .alert-danger {
            background: rgba(230,57,70,0.12);
            border-color: rgba(230,57,70,0.25);
            color: #ff6b7a;
        }
        .alert-info {
            background: rgba(13,110,253,0.12);
            border-color: rgba(13,110,253,0.25);
            color: #6db3f2;
        }
        .alert-warning {
            background: rgba(212,168,67,0.12);
            border-color: rgba(212,168,67,0.25);
            color: var(--tdr-gold);
        }
        .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

        /* ── Badges ──────────────────────────────────────────────── */
        .badge { font-weight: 600; font-size: 0.75rem; border-radius: 6px; letter-spacing: 0.3px; }
        .badge-pending   { background: rgba(212,168,67,0.2) !important; color: var(--tdr-gold) !important; }
        .badge-paid      { background: rgba(13,110,253,0.2) !important; color: #6db3f2 !important; }
        .badge-shipped   { background: rgba(13,202,240,0.2) !important; color: #5df0ff !important; }
        .badge-processing{ background: rgba(111,66,193,0.2) !important; color: #b38df7 !important; }
        .badge-delivered { background: rgba(25,135,84,0.2) !important; color: #5dd39e !important; }
        .badge-cancelled { background: rgba(108,117,125,0.2) !important; color: #adb5bd !important; }
        .badge-failed    { background: rgba(230,57,70,0.2) !important; color: #ff6b7a !important; }

        /* ── Tables ──────────────────────────────────────────────── */
        .table { color: var(--tdr-text); --bs-table-bg: transparent; }
        .table thead { border-bottom: 1px solid var(--tdr-border); }
        .table thead th {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--tdr-muted);
            padding: 0.875rem 1rem;
            border: none;
        }
        .table tbody td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--tdr-border);
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background: rgba(255,255,255,0.03);
            --bs-table-hover-bg: rgba(255,255,255,0.03);
        }
        .table-light { --bs-table-bg: rgba(255,255,255,0.03); }

        /* ── Pagination ──────────────────────────────────────────── */
        .pagination .page-link {
            background: rgba(255,255,255,0.05);
            border-color: var(--tdr-border);
            color: var(--tdr-muted);
            font-size: 0.85rem;
        }
        .pagination .page-item.active .page-link {
            background: var(--tdr-red);
            border-color: var(--tdr-red);
            color: #fff;
        }
        .pagination .page-link:hover {
            background: rgba(255,255,255,0.1);
            color: var(--tdr-text);
        }

        /* ── List Groups ─────────────────────────────────────────── */
        .list-group-item {
            background: transparent;
            border-color: var(--tdr-border);
            color: var(--tdr-text);
        }

        /* ── Footer ──────────────────────────────────────────────── */
        .tdr-footer {
            background: var(--tdr-surface);
            border-top: 1px solid var(--tdr-border);
            padding: 48px 0 24px;
            margin-top: 80px;
        }
        .tdr-footer h6 {
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--tdr-muted);
            margin-bottom: 16px;
        }
        .tdr-footer a {
            color: var(--tdr-muted);
            text-decoration: none;
            font-size: 0.875rem;
            display: block;
            padding: 3px 0;
            transition: color 0.2s ease;
        }
        .tdr-footer a:hover { color: #fff; }
        .tdr-footer .footer-brand {
            font-weight: 800;
            font-size: 1.1rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .tdr-footer .footer-brand .brand-mark {
            background: var(--tdr-red);
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .tdr-footer .footer-bottom {
            border-top: 1px solid var(--tdr-border);
            padding-top: 20px;
            margin-top: 32px;
        }

        /* ── Text Utilities ──────────────────────────────────────── */
        .text-muted { color: var(--tdr-muted) !important; }
        .text-danger { color: var(--tdr-red) !important; }
        a { color: var(--tdr-red); }
        a:hover { color: var(--tdr-red-hover); }
        code { color: var(--tdr-gold); background: rgba(212,168,67,0.1); padding: 2px 6px; border-radius: 4px; font-size: 0.85em; }
        hr { border-color: var(--tdr-border); opacity: 1; }

        /* ── Scrollbar ───────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--tdr-dark); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        /* ── Animations ──────────────────────────────────────────── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp 0.4s ease forwards; }

        /* ── Main Container Spacing ──────────────────────────────── */
        .tdr-main-alerts { max-width: 1200px; margin: 0 auto; padding: 1rem 1rem 0; }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg tdr-navbar">
    <div class="container">
        <a class="navbar-brand" href="/">
            <span class="brand-mark"><i class="bi bi-gear-fill"></i></span>
            TDR HPZ
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Beranda</a></li>
                @auth
                    @if(auth()->user()->role !== 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('shop') }}">
                            <i class="bi bi-grid-3x3-gap me-1"></i>Produk
                        </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}">
                            <i class="bi bi-clock-history me-1"></i>Histori
                        </a></li>
                    @endif
                    @if(auth()->user()->role === 'customer')
                        <li class="nav-item"><a class="nav-link" href="{{ route('affiliate.register.form') }}">
                            <i class="bi bi-people me-1"></i>Jadi Affiliate
                        </a></li>
                    @endif
                    @if(auth()->user()->role === 'affiliate')
                        <li class="nav-item"><a class="nav-link" href="{{ route('affiliate.dashboard') }}">
                            <i class="bi bi-bar-chart-line me-1"></i>Dashboard Affiliate
                        </a></li>
                    @endif
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>Admin Panel
                        </a></li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav align-items-center gap-2">
                @auth
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="user-info">
                            <i class="bi bi-person-circle"></i>
                            {{ auth()->user()->name }}
                            @if(!auth()->user()->telegram_chat_id)
                                <span class="badge bg-warning text-dark" title="Hubungkan Telegram" style="font-size:.6rem">
                                    <i class="bi bi-telegram"></i>
                                </span>
                            @else
                                <span class="badge" style="background:rgba(25,135,84,0.2);color:#5dd39e;font-size:.6rem">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button class="btn btn-outline-light btn-sm">
                                <i class="bi bi-box-arrow-right me-1"></i>Keluar
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-register" href="{{ route('register') }}">Daftar</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main>
    <div class="tdr-main-alerts">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show animate-in">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show animate-in">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show animate-in">
                <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-warning animate-in">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
    </div>
    @yield('content')
</main>

<footer class="tdr-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <span class="brand-mark"><i class="bi bi-gear-fill"></i></span>
                    TDR HPZ Store
                </div>
                <p style="color:var(--tdr-muted);font-size:.875rem;line-height:1.6;max-width:300px">
                    Distributor resmi spare part motor berkualitas tinggi.
                    Harga kompetitif dengan pengiriman ke seluruh Indonesia.
                </p>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6>Navigasi</h6>
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('shop') }}">Produk</a>
                @guest
                    <a href="{{ route('register') }}">Daftar</a>
                    <a href="{{ route('login') }}">Masuk</a>
                @endguest
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6>Layanan</h6>
                <a href="{{ route('shop') }}">Katalog</a>
                @auth
                    <a href="{{ route('affiliate.register.form') }}">Affiliate</a>
                    <a href="{{ route('profile.edit') }}">Profil Saya</a>
                @endauth
            </div>
            <div class="col-lg-4 col-md-6">
                <h6>Kontak</h6>
                <div style="color:var(--tdr-muted);font-size:.875rem;line-height:1.8">
                    <div><i class="bi bi-geo-alt me-2"></i>Jakarta, Indonesia</div>
                    <div><i class="bi bi-telegram me-2"></i>Notifikasi via Telegram</div>
                    <div><i class="bi bi-shield-check me-2"></i>Pembayaran aman via Midtrans</div>
                </div>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <small style="color:var(--tdr-muted)">&copy; {{ date('Y') }} TDR HPZ Store. All rights reserved.</small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
