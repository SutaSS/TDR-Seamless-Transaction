@extends('layouts.admin')
@section('title', 'Affiliates')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Daftar Affiliate</h4>
    @if(isset($pendingCount) && $pendingCount > 0)
        <span class="badge" style="background:rgba(245,158,11,0.15);color:#fbbf24;font-size:.82rem">
            <i class="bi bi-hourglass-split me-1"></i>{{ $pendingCount }} menunggu approval
        </span>
    @endif
</div>

{{-- Filter Status --}}
<div class="mb-3 d-flex gap-2 align-items-center">
    <span class="fw-semibold small text-muted">Filter:</span>
    @foreach(['' => 'Semua', 'pending' => 'Pending', 'active' => 'Active', 'suspended' => 'Suspended'] as $val => $label)
        <a href="{{ route('admin.affiliates') }}{{ $val ? '?status='.$val : '' }}"
           class="btn btn-sm {{ request('status', '') === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
           {{ $label }}
        </a>
    @endforeach
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kode Referral</th>
                    <th>Rekening</th>
                    <th>Clicks</th>
                    <th>Konversi</th>
                    <th>Total Komisi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($affiliates as $aff)
                @php
                    $statusStyle = match($aff->status) {
                        'active'    => ['rgba(16,185,129,0.15)', '#34d399'],
                        'pending'   => ['rgba(245,158,11,0.15)', '#fbbf24'],
                        'suspended' => ['rgba(230,57,70,0.15)', '#ff6b7a'],
                        default     => ['rgba(107,114,128,0.15)', '#9ca3af'],
                    };
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $aff->user?->name ?? '-' }}</div>
                        <small class="text-muted">{{ $aff->user?->email }}</small>
                    </td>
                    <td><code>{{ $aff->referral_code }}</code></td>
                    <td class="small">
                        @if($aff->bank_account_number)
                            <div>{{ strtoupper(str_replace('_', ' ', $aff->bank_name ?? '')) }}</div>
                            <div class="fw-semibold">{{ $aff->bank_account_number }}</div>
                            <div class="text-muted">a/n {{ $aff->bank_account_holder }}</div>
                        @else
                            <span class="text-muted">--</span>
                        @endif
                    </td>
                    <td>{{ $aff->referral_clicks_count }}</td>
                    <td>{{ $aff->conversions_count }}</td>
                    <td class="fw-semibold" style="color:#5dd39e">Rp {{ number_format($aff->total_commission_amount ?? 0, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge" style="background:{{ $statusStyle[0] }};color:{{ $statusStyle[1] }}">{{ $aff->status }}</span>
                        @if($aff->approved_at)
                            <div class="text-muted" style="font-size:.7rem">{{ $aff->approved_at?->format('d/m/Y') }}</div>
                        @endif
                    </td>
                    <td>
                        @if($aff->status === 'pending')
                            <div class="d-flex gap-1">
                                <form method="POST" action="{{ route('admin.affiliates.approve', $aff) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm py-0 px-2"
                                        onclick="return confirm('Approve affiliate {{ $aff->user?->name }}?')">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.affiliates.reject', $aff) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm py-0 px-2"
                                        onclick="return confirm('Tolak affiliate {{ $aff->user?->name }}?')">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        @elseif($aff->status === 'active')
                            <form method="POST" action="{{ route('admin.affiliates.reject', $aff) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2"
                                    onclick="return confirm('Nonaktifkan affiliate ini?')">
                                    Nonaktifkan
                                </button>
                            </form>
                        @elseif($aff->status === 'suspended')
                            <form method="POST" action="{{ route('admin.affiliates.approve', $aff) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm py-0 px-2">
                                    Reaktifkan
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-5">Belum ada affiliate</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $affiliates->links() }}</div>
</div>
@endsection
