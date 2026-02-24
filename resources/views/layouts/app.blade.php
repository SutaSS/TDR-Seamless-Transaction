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
        .badge-delivered { background:#198754; }
        .badge-failed    { background:#dc3545; }
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
                <li class="nav-item"><a class="nav-link" href="/">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="/checkout">Checkout</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('affiliate.register.form') }}">Affiliate</a></li>
            </ul>
            <ul class="navbar-nav">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                                @csrf <button class="btn btn-sm btn-outline-light ms-2">Logout</button>
                            </form>
                        </li>
                    @endif
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.login') }}">Login Admin</a></li>
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
