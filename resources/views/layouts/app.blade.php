<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TDR HPZ Store')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar-brand { font-weight: 700; color: #e63946 !important; }
        .hero-section { background: linear-gradient(135deg, #1d3557 0%, #457b9d 100%); color: #fff; padding: 60px 0; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .badge-pending   { background:#ffc107; color:#000; }
        .badge-paid      { background:#0d6efd; }
        .badge-shipped   { background:#0dcaf0; color:#000; }
        .badge-pending    { background:#ffc107; color:#000; }
        .badge-processing { background:#6f42c1; color:#fff; }
        .badge-shipped    { background:#0dcaf0; color:#000; }
        .badge-delivered  { background:#198754; color:#fff; }
        .badge-cancelled  { background:#6c757d; color:#fff; }
        .badge-failed     { background:#dc3545; color:#fff; }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">🏍 TDR HPZ</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Beranda</a></li>
                @auth
                    @if(auth()->user()->role !== 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('shop') }}">
                            <i class="bi bi-shop"></i> Beli Sekarang
                        </a></li>
                    @endif
                    @if(auth()->user()->role === 'customer')
                        <li class="nav-item"><a class="nav-link" href="{{ route('affiliate.register.form') }}">
                            <i class="bi bi-people"></i> Jadi Affiliate
                        </a></li>
                    @endif
                    @if(auth()->user()->role === 'affiliate')
                        <li class="nav-item"><a class="nav-link" href="{{ route('affiliate.dashboard') }}">
                            <i class="bi bi-graph-up"></i> Dashboard Affiliate
                        </a></li>
                    @endif
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Admin Panel
                        </a></li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav align-items-center">
                @auth
                    <li class="nav-item me-2">
                        <a href="{{ route('profile.edit') }}" class="navbar-text text-light small text-decoration-none d-flex align-items-center gap-1">
                            <i class="bi bi-person-circle"></i>
                            {{ auth()->user()->name }}
                            @if(!auth()->user()->telegram_chat_id)
                                <span class="badge bg-warning text-dark ms-1" title="Hubungkan Telegram">
                                    <i class="bi bi-telegram"></i>
                                </span>
                            @else
                                <span class="badge bg-success ms-1" title="Telegram terhubung" style="font-size:.65rem">✓</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-light">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-sm btn-primary ms-1" href="{{ route('register') }}">
                            Daftar
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main>
    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-warning">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
    </div>
    @yield('content')
</main>

<footer class="bg-dark text-white text-center py-3 mt-5">
    <small>&copy; {{ date('Y') }} TDR HPZ — Spare Part Motor Berkualitas</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
