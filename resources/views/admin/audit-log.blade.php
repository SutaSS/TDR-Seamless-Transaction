@extends('layouts.admin')
@section('title', 'Audit Log')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Audit Log</h4>
    <span class="text-muted small">Riwayat perubahan status pesanan</span>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.audit-log') }}" class="mb-3 d-flex gap-2">
    <div class="input-group" style="max-width:360px">
        <span class="input-group-text" style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1)">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. pesanan / status / catatan…"
            class="form-control form-control-sm"
            style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1);color:#e2e8f0">
    </div>
    <button class="btn btn-sm btn-outline-secondary">Cari</button>
    @if(request('search'))
        <a href="{{ route('admin.audit-log') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
    @endif
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:160px">Waktu</th>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Aksi / Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="text-muted small" style="white-space:nowrap">
                        {{ $log->created_at?->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') ?? '—' }}
                    </td>
                    <td>
                        @if($log->order)
                            <a href="{{ route('admin.orders.show', $log->order) }}" class="fw-semibold small">
                                {{ $log->order->order_number }}
                            </a>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="small">{{ $log->order?->customer?->name ?? '—' }}</td>
                    <td>
                        @php
                            $title = $log->status_title ?? '';
                            $color = '#e2e8f0';
                            if (str_contains(strtolower($title), 'completed') || str_contains(strtolower($title), 'selesai')) $color = '#34d399';
                            elseif (str_contains(strtolower($title), 'shipped') || str_contains(strtolower($title), 'dikirim')) $color = '#22d3ee';
                            elseif (str_contains(strtolower($title), 'cancel')) $color = '#ff6b7a';
                            elseif (str_contains(strtolower($title), 'processing')) $color = '#a78bfa';
                        @endphp
                        <span style="color:{{ $color }};font-size:.82rem;font-weight:600">{{ $title }}</span>
                    </td>
                    <td class="text-muted small">{{ $log->description ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-journal-text fs-3 d-block mb-2 opacity-50"></i>
                        Belum ada audit log.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer d-flex justify-content-center py-2">
            <div class="d-flex align-items-center gap-3">
                @if($logs->onFirstPage())
                    <span class="btn btn-sm btn-outline-secondary disabled">‹ Sebelumnya</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}&search={{ request('search') }}" class="btn btn-sm btn-outline-secondary">‹ Sebelumnya</a>
                @endif
                <span class="text-muted small">{{ $logs->currentPage() }} / {{ $logs->lastPage() }}</span>
                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}&search={{ request('search') }}" class="btn btn-sm btn-outline-secondary">Berikutnya ›</a>
                @else
                    <span class="btn btn-sm btn-outline-secondary disabled">Berikutnya ›</span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
