@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<h4 class="mb-4 fw-bold">Dashboard</h4>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card blue p-3">
            <div class="text-muted small">Total Pesanan</div>
            <div class="fs-4 fw-bold">{{ $stats['total_orders'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card green p-3">
            <div class="text-muted small">Revenue</div>
            <div class="fs-5 fw-bold">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card orange p-3">
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-bold">{{ $stats['pending_orders'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card red p-3">
            <div class="text-muted small">Total Affiliate</div>
            <div class="fs-4 fw-bold">{{ $stats['total_affiliates'] }}</div>
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
                    <thead class="table-light">
                        <tr><th>No. Pesanan</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Waktu</th></tr>
                    </thead>
                    <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_number }}</a></td>
                            <td>{{ $order->customer_name ?? $order->customer?->name ?? '-' }}</td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td><span class="badge badge-{{ $order->order_status }}">{{ $order->order_status }}</span></td>
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
        <div class="card">
            <div class="card-header"><strong>Top Affiliates</strong></div>
            <ul class="list-group list-group-flush">
            @forelse($affiliatePerformance as $aff)
                <li class="list-group-item d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold">{{ $aff->user?->name ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $aff->conversions_count }} konversi</small>
                    </div>
                    <span class="text-success fw-bold">Rp {{ number_format($aff->conversions_sum_commission_amount ?? 0, 0, ',', '.') }}</span>
                </li>
            @empty
                <li class="list-group-item text-muted text-center py-3">Belum ada affiliate</li>
            @endforelse
            </ul>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Status Pesanan</h6>
                @foreach(['pending'=>'warning','paid'=>'primary','shipped'=>'info','delivered'=>'success'] as $st => $color)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="badge bg-{{ $color }}">{{ $st }}</span>
                        <span>{{ $stats[$st.'_orders'] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
