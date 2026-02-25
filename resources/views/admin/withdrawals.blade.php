@extends('layouts.admin')
@section('title', 'Cairkan Dana Affiliate')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Cairkan Dana Affiliate</h4>
    @if($pendingCount > 0)
        <span class="badge" style="background:rgba(245,158,11,0.15);color:#fbbf24;font-size:.82rem">
            <i class="bi bi-hourglass-split me-1"></i>{{ $pendingCount }} menunggu diproses
        </span>
    @endif
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert">
        <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filter Status --}}
<div class="mb-3 d-flex gap-2 align-items-center flex-wrap">
    <span class="fw-semibold small text-muted">Filter:</span>
    @foreach(['pending' => 'Pending', 'completed' => 'Selesai', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $val => $label)
        <a href="{{ route('admin.withdrawals') }}?status={{ $val }}"
           class="btn btn-sm {{ $status === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
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
                    <th>Jumlah</th>
                    <th>Rekening Tujuan</th>
                    <th>Diajukan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($withdrawals as $w)
                @php
                    $statusStyle = match($w->status) {
                        'completed' => ['rgba(16,185,129,0.15)', '#34d399'],
                        'pending'   => ['rgba(245,158,11,0.15)', '#fbbf24'],
                        'rejected'  => ['rgba(230,57,70,0.15)',  '#ff6b7a'],
                        default     => ['rgba(107,114,128,0.15)','#9ca3af'],
                    };
                    $amountFormatted = 'Rp ' . number_format((float)$w->amount, 0, ',', '.');
                    $safeName        = addslashes($w->affiliate?->name ?? '');
                    $statusLabel = match($w->status) {
                        'completed' => 'Selesai',
                        'pending'   => 'Pending',
                        'rejected'  => 'Ditolak',
                        default     => $w->status,
                    };
                @endphp
                <tr>
                    {{-- Affiliate info --}}
                    <td>
                        <div class="fw-semibold">{{ $w->affiliate?->name ?? '-' }}</div>
                        <small class="text-muted">{{ $w->affiliate?->email }}</small>
                        @if($w->affiliate?->affiliateProfile?->referral_code)
                            <div><code style="font-size:.7rem">{{ $w->affiliate->affiliateProfile->referral_code }}</code></div>
                        @endif
                    </td>

                    {{-- Amount --}}
                    <td class="fw-bold" style="color:#5dd39e;white-space:nowrap">
                        Rp {{ number_format((float)$w->amount, 0, ',', '.') }}
                    </td>

                    {{-- Bank info --}}
                    <td class="small">
                        <div class="fw-semibold">{{ strtoupper(str_replace('_', ' ', $w->bank_name ?? '')) }}</div>
                        <div>{{ $w->bank_account_number }}</div>
                        <div class="text-muted">a/n {{ $w->bank_account_holder }}</div>
                    </td>

                    {{-- Date --}}
                    <td class="small text-muted" style="white-space:nowrap">
                        {{ $w->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="badge" style="background:{{ $statusStyle[0] }};color:{{ $statusStyle[1] }}">
                            {{ $statusLabel }}
                        </span>
                        @if($w->processed_at)
                            <div class="text-muted" style="font-size:.7rem">
                                {{ $w->processed_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        @if($w->rejection_reason)
                            <div class="text-muted" style="font-size:.7rem;max-width:140px" title="{{ $w->rejection_reason }}">
                                {{ Str::limit($w->rejection_reason, 40) }}
                            </div>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td>
                        @if($w->status === 'pending')
                            <div class="d-flex gap-1 flex-wrap">
                                {{-- Approve --}}
                                <form method="POST" action="{{ route('admin.withdrawals.approve', $w) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm py-0 px-2"
                                        onclick="return confirm('Setujui pencairan {{ $amountFormatted }} untuk {{ $safeName }}?')">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                </form>

                                {{-- Reject with reason modal trigger --}}
                                <button type="button" class="btn btn-danger btn-sm py-0 px-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal{{ $w->id }}">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </div>

                            {{-- Reject Modal --}}
                            <div class="modal fade" id="rejectModal{{ $w->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="background:#1e1e2e;border:1px solid rgba(255,255,255,.1)">
                                        <div class="modal-header border-secondary">
                                            <h5 class="modal-title fw-bold">Tolak Pencairan</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.withdrawals.reject', $w) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <p class="text-muted small mb-3">
                                                    Penolakan akan mengembalikan saldo
                                                    <strong style="color:#5dd39e">Rp {{ number_format((float)$w->amount, 0, ',', '.') }}</strong>
                                                    ke akun <strong>{{ $w->affiliate?->name }}</strong>.
                                                </p>
                                                <label class="form-label fw-semibold small">Alasan penolakan</label>
                                                <textarea name="rejection_reason" rows="3"
                                                    class="form-control form-control-sm"
                                                    style="background:#2a2a3e;border-color:rgba(255,255,255,.15);color:#e2e8f0"
                                                    placeholder="Contoh: Nomor rekening tidak valid..."></textarea>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-x-lg me-1"></i>Konfirmasi Tolak
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                        Tidak ada permintaan pencairan
                        @if($status !== 'all') dengan status <strong>{{ $status }}</strong>@endif.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($withdrawals->hasPages())
        <div class="card-footer d-flex justify-content-center py-2">
            <div class="d-flex align-items-center gap-3">
                @if($withdrawals->onFirstPage())
                    <span class="btn btn-sm btn-outline-secondary disabled">‹ Sebelumnya</span>
                @else
                    <a href="{{ $withdrawals->previousPageUrl() }}&status={{ $status }}" class="btn btn-sm btn-outline-secondary">‹ Sebelumnya</a>
                @endif
                <span class="text-muted small">{{ $withdrawals->currentPage() }} / {{ $withdrawals->lastPage() }}</span>
                @if($withdrawals->hasMorePages())
                    <a href="{{ $withdrawals->nextPageUrl() }}&status={{ $status }}" class="btn btn-sm btn-outline-secondary">Berikutnya ›</a>
                @else
                    <span class="btn btn-sm btn-outline-secondary disabled">Berikutnya ›</span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
