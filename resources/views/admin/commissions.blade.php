@extends('layouts.admin')
@section('title', 'Komisi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Komisi Afiliasi</h4>
</div>

{{-- Summary cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card p-3">
            <div class="small text-muted mb-1">Pending</div>
            <div class="fw-bold" style="color:#fbbf24">Rp {{ number_format($summary['pending'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3">
            <div class="small text-muted mb-1">Sudah Dikreditkan</div>
            <div class="fw-bold" style="color:#34d399">Rp {{ number_format($summary['earned'], 0, ',', '.') }}</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="mb-3 d-flex gap-2 align-items-center flex-wrap">
    <span class="fw-semibold small text-muted">Filter:</span>
    @foreach(['pending' => 'Pending', 'earned' => 'Dikreditkan', '' => 'Semua'] as $val => $label)
        <a href="{{ route('admin.commissions') }}?status={{ $val }}"
            class="btn btn-sm {{ request('status', 'pending') === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Affiliate</th>
                    <th>Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Jumlah</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
            @forelse($commissions as $c)
                @php
                    $statusStyle = match($c->status) {
                        'earned'  => ['rgba(16,185,129,0.15)', '#34d399'],
                        'pending' => ['rgba(245,158,11,0.15)', '#fbbf24'],
                        default   => ['rgba(107,114,128,0.15)','#9ca3af'],
                    };
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold small">{{ $c->affiliate?->name ?? '—' }}</div>
                        @if($c->affiliate?->affiliateProfile?->referral_code)
                            <code style="font-size:.7rem">{{ $c->affiliate->affiliateProfile->referral_code }}</code>
                        @endif
                    </td>
                    <td>
                        @if($c->order)
                            <a href="{{ route('admin.orders.show', $c->order) }}" class="small fw-semibold">
                                {{ $c->order->order_number }}
                            </a>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $c->order?->customer?->name ?? '—' }}</td>
                    <td class="fw-bold" style="color:#5dd39e;white-space:nowrap">
                        Rp {{ number_format((float)$c->amount, 0, ',', '.') }}
                    </td>
                    <td class="small text-muted">{{ $c->commission_rate }}%</td>
                    <td>
                        <span class="badge" style="background:{{ $statusStyle[0] }};color:{{ $statusStyle[1] }}">
                            {{ $c->status === 'earned' ? 'Dikreditkan' : 'Pending' }}
                        </span>
                    </td>
                    <td class="text-muted small" style="white-space:nowrap">
                        {{ ($c->earned_at ?? $c->created_at)?->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-percent fs-3 d-block mb-2 opacity-50"></i>
                        Tidak ada data komisi.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions->hasPages())
        <div class="card-footer d-flex justify-content-center py-2">
            <div class="d-flex align-items-center gap-3">
                @if($commissions->onFirstPage())
                    <span class="btn btn-sm btn-outline-secondary disabled">‹ Sebelumnya</span>
                @else
                    <a href="{{ $commissions->previousPageUrl() }}&status={{ request('status') }}" class="btn btn-sm btn-outline-secondary">‹ Sebelumnya</a>
                @endif
                <span class="text-muted small">{{ $commissions->currentPage() }} / {{ $commissions->lastPage() }}</span>
                @if($commissions->hasMorePages())
                    <a href="{{ $commissions->nextPageUrl() }}&status={{ request('status') }}" class="btn btn-sm btn-outline-secondary">Berikutnya ›</a>
                @else
                    <span class="btn btn-sm btn-outline-secondary disabled">Berikutnya ›</span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
