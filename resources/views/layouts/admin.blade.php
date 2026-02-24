<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — TDR HPZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .sidebar { width: 220px; min-height: 100vh; background: #1d3557; color: #fff; }
        .sidebar a { color: rgba(255,255,255,.8); text-decoration: none; padding: 10px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,.1); color: #fff; }
        .sidebar .brand { padding: 20px; font-size: 1.2rem; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,.2); }
        main { flex: 1; background: #f8f9fa; }
        .stat-card { border-left: 4px solid; }
        .stat-card.blue   { border-color: #0d6efd; }
        .stat-card.green  { border-color: #198754; }
        .stat-card.orange { border-color: #fd7e14; }
        .stat-card.red    { border-color: #dc3545; }
        /* Order status badges */
        .badge-pending    { background:#ffc107!important; color:#000!important; }
        .badge-processing { background:#6f42c1!important; color:#fff!important; }
        .badge-shipped    { background:#0dcaf0!important; color:#000!important; }
        .badge-delivered  { background:#198754!important; color:#fff!important; }
        .badge-cancelled  { background:#6c757d!important; color:#fff!important; }
        .badge-paid       { background:#0d6efd!important; color:#fff!important; }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <div class="sidebar">
        <div class="brand">🏍 TDR Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
            <i class="bi bi-bag"></i> Pesanan
        </a>
        <a href="{{ route('admin.affiliates') }}" class="{{ request()->routeIs('admin.affiliates') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Affiliates
        </a>
        <a href="{{ route('admin.notifications') }}" class="{{ request()->routeIs('admin.notifications') ? 'active' : '' }}">
            <i class="bi bi-bell"></i> Notifikasi
        </a>
        <hr style="border-color:rgba(255,255,255,.3)">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn btn-link sidebar-link w-100 text-start" style="color:rgba(255,255,255,.7);text-decoration:none;padding:10px 20px;">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
    <main class="p-4 w-100">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-warning">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
