@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Dashboard</h4>
    <span class="text-muted small">{{ now()->format('d M Y') }}</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card blue">
            <div class="stat-label">Total Pesanan</div>
            <div class="stat-value">{{ $stats['total_orders'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card green">
            <div class="stat-label">Revenue</div>
            <div class="stat-value" style="font-size:1.15rem">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card orange">
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ $stats['pending_orders'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card red">
            <div class="stat-label">Total Affiliate</div>
            <div class="stat-value">{{ $stats['total_affiliates'] }}</div>
            @if($stats['pending_affiliates'] > 0)
                <a href="{{ route('admin.affiliates') }}?status=pending" class="badge text-decoration-none mt-2" style="background:rgba(245,158,11,0.15);color:#fbbf24;font-size:.7rem">
                    <i class="bi bi-hourglass-split me-1"></i>{{ $stats['pending_affiliates'] }} menunggu approval
                </a>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Pesanan Terbaru</strong>
                <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>No. Pesanan</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Waktu</th></tr>
                    </thead>
                    <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_number }}</a></td>
                            <td>{{ $order->customer?->name ?? '-' }}</td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $order->status }}">{{ $order->status }}</span>
                                @if($order->payment_verified_at)
                                    <span class="badge badge-paid ms-1" style="font-size:.65rem">paid</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $order->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pesanan</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header"><strong>Top Affiliates</strong></div>
            <ul class="list-group list-group-flush">
            @forelse($affiliatePerformance as $aff)
                <li class="list-group-item d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold">{{ $aff->user?->name ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $aff->conversions_count }} konversi</small>
                    </div>
                    <span style="color:#5dd39e" class="fw-bold">Rp {{ number_format($aff->commissions_sum_amount ?? 0, 0, ',', '.') }}</span>
                </li>
            @empty
                <li class="list-group-item text-muted text-center py-3">Belum ada affiliate</li>
            @endforelse
            </ul>
        </div>

        <div class="card">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-3">Status Pesanan</h6>
                @foreach([
                    'pending'    => ['#fbbf24', 'rgba(245,158,11,0.15)'],
                    'processing' => ['#a78bfa', 'rgba(139,92,246,0.15)'],
                    'shipped'    => ['#22d3ee', 'rgba(6,182,212,0.15)'],
                    'delivered'  => ['#34d399', 'rgba(16,185,129,0.15)'],
                    'cancelled'  => ['#9ca3af', 'rgba(107,114,128,0.15)'],
                ] as $st => [$color, $bg])
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge" style="background:{{ $bg }};color:{{ $color }}">{{ $st }}</span>
                        <span class="fw-semibold small">{{ $stats[$st.'_orders'] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
