@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Dasbor</h4>
    <span class="text-muted small">{{ now()->setTimezone('Asia/Jakarta')->format('d M Y') }}</span>
</div>

{{-- Row 1: Orders today / month / revenue today --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-bag-check" style="color:#a78bfa"></i>
                <span class="small text-muted">Pesanan Hari Ini</span>
            </div>
            <div class="fw-bold" style="font-size:1.9rem;letter-spacing:-.5px">{{ $stats['orders_today'] }}</div>
            <div class="text-muted" style="font-size:.78rem">Total pesanan masuk hari ini</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-calendar3" style="color:#22d3ee"></i>
                <span class="small text-muted">Pesanan Bulan Ini</span>
            </div>
            <div class="fw-bold" style="font-size:1.9rem;letter-spacing:-.5px">{{ $stats['orders_month'] }}</div>
            <div class="text-muted" style="font-size:.78rem">Total pesanan bulan {{ now()->setTimezone('Asia/Jakarta')->isoFormat('MMMM YYYY') }}</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-info-circle" style="color:#f87171"></i>
                <span class="small text-muted">Revenue Hari Ini</span>
            </div>
            <div class="fw-bold" style="font-size:1.4rem;letter-spacing:-.5px;color:#5dd39e">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</div>
            <div class="text-muted" style="font-size:.78rem">Dari pesanan selesai hari ini</div>
        </div>
    </div>
</div>

{{-- Row 2: Revenue month / pending / perlu diproses --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-credit-card" style="color:#fbbf24"></i>
                <span class="small text-muted">Revenue Bulan Ini</span>
            </div>
            <div class="fw-bold" style="font-size:1.4rem;letter-spacing:-.5px;color:#5dd39e">Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</div>
            <div class="text-muted" style="font-size:.78rem">Dari pesanan selesai bulan ini</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-hourglass-split" style="color:#fbbf24"></i>
                <span class="small text-muted">Pesanan Pending</span>
            </div>
            <div class="fw-bold" style="font-size:1.9rem;letter-spacing:-.5px;color:#fbbf24">{{ $stats['pending_orders'] }}</div>
            <div class="text-muted" style="font-size:.78rem">Menunggu pembayaran</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-gear" style="color:#f87171"></i>
                <span class="small text-muted">Perlu Diproses</span>
            </div>
            <div class="fw-bold" style="font-size:1.9rem;letter-spacing:-.5px;color:#f87171">{{ $stats['verified_orders'] + $stats['processing_orders'] }}</div>
            <div class="text-muted" style="font-size:.78rem">Terverifikasi &amp; sedang diproses</div>
        </div>
    </div>
</div>

{{-- Row 3: Afiliasi aktif / menunggu persetujuan / penarikan pending --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-people" style="color:#34d399"></i>
                <span class="small text-muted">Afiliasi Aktif</span>
            </div>
            <div class="fw-bold" style="font-size:1.9rem;letter-spacing:-.5px">{{ $stats['active_affiliates'] }}</div>
            <div class="text-muted" style="font-size:.78rem">Total afiliasi aktif</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-person-check" style="color:#a78bfa"></i>
                <span class="small text-muted">Menunggu Persetujuan</span>
            </div>
            <div class="fw-bold" style="font-size:1.9rem;letter-spacing:-.5px;color:#a78bfa">{{ $stats['pending_affiliates'] }}</div>
            <div class="text-muted" style="font-size:.78rem">Afiliasi baru menunggu</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-cash-stack" style="color:#f87171"></i>
                <span class="small text-muted">Penarikan Pending</span>
            </div>
            <div class="fw-bold" style="font-size:1.9rem;letter-spacing:-.5px;color:#f87171">{{ $stats['pending_withdrawals'] }}</div>
            <div class="text-muted" style="font-size:.78rem">Penarikan belum diproses</div>
        </div>
    </div>
</div>

{{-- Row 4: Komisi bulan ini / total saldo afiliasi --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-graph-up" style="color:#22d3ee"></i>
                <span class="small text-muted">Komisi Bulan Ini</span>
            </div>
            <div class="fw-bold" style="font-size:1.4rem;letter-spacing:-.5px;color:#5dd39e">Rp {{ number_format($stats['commission_month'], 0, ',', '.') }}</div>
            <div class="text-muted" style="font-size:.78rem">Total komisi terbayar bulan ini</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-wallet2" style="color:#fbbf24"></i>
                <span class="small text-muted">Total Saldo Afiliasi</span>
            </div>
            <div class="fw-bold" style="font-size:1.4rem;letter-spacing:-.5px;color:#5dd39e">Rp {{ number_format($stats['total_affiliate_balance'], 0, ',', '.') }}</div>
            <div class="text-muted" style="font-size:.78rem">Saldo belum ditarik semua afiliasi</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-box-seam" style="color:#a78bfa"></i>
                <span class="small text-muted">Produk Aktif</span>
            </div>
            <div class="fw-bold" style="font-size:1.9rem;letter-spacing:-.5px">{{ $stats['active_products'] }}</div>
            <div class="text-muted" style="font-size:.78rem">Produk yang sedang aktif dijual</div>
        </div>
    </div>
</div>

{{-- Bottom: Recent Orders + Top Affiliates --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Pesanan Terbaru</strong>
                <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="input-group px-3 pt-3 pb-2">
                <span class="input-group-text" style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1)"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="orderSearch" class="form-control form-control-sm" placeholder="Cari"
                    style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1);color:#e2e8f0">
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Nomor Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Afiliasi</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold">{{ $order->order_number }}</a></td>
                            <td>{{ $order->customer?->name ?? '-' }}</td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td><span class="badge badge-{{ $order->status }}">{{ $order->status }}</span></td>
                            <td>
                                @if($order->affiliate)
                                    <span class="badge" style="background:rgba(167,139,250,0.15);color:#a78bfa;font-size:.7rem">
                                        {{ $order->affiliate->affiliateProfile?->referral_code ?? $order->affiliate->name }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-muted small" style="white-space:nowrap">{{ $order->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><strong>Top Affiliates</strong></div>
            <ul class="list-group list-group-flush">
            @forelse($affiliatePerformance as $aff)
                <li class="list-group-item d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold small">{{ $aff->user?->name ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $aff->conversions_count }} konversi</small>
                    </div>
                    <span style="color:#5dd39e" class="fw-bold small">Rp {{ number_format($aff->commissions_sum_amount ?? 0, 0, ',', '.') }}</span>
                </li>
            @empty
                <li class="list-group-item text-muted text-center py-3 small">Belum ada affiliate</li>
            @endforelse
            </ul>
        </div>

        <div class="card">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-3">Status Pesanan</h6>
                @foreach([
                    'pending'    => ['#fbbf24', 'rgba(245,158,11,0.15)',    'pending_orders'],
                    'verified'   => ['#22d3ee', 'rgba(6,182,212,0.15)',     'verified_orders'],
                    'processing' => ['#a78bfa', 'rgba(139,92,246,0.15)',    'processing_orders'],
                    'shipped'    => ['#38bdf8', 'rgba(56,189,248,0.15)',    'shipped_orders'],
                    'completed'  => ['#34d399', 'rgba(16,185,129,0.15)',    'completed_orders'],
                    'cancelled'  => ['#9ca3af', 'rgba(107,114,128,0.15)',   'cancelled_orders'],
                ] as $st => [$color, $bg, $key])
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge" style="background:{{ $bg }};color:{{ $color }}">{{ $st }}</span>
                        <span class="fw-semibold small">{{ $stats[$key] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('orderSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#ordersTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
