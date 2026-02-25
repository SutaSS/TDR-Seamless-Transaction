@extends('layouts.admin')
@section('title', 'Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Pengguna</h4>
    <span class="text-muted small">{{ $users->total() }} total</span>
</div>

{{-- Search + filter role --}}
<form method="GET" action="{{ route('admin.users') }}" class="mb-3 d-flex gap-2 flex-wrap">
    <div class="input-group" style="max-width:300px">
        <span class="input-group-text" style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1)">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email…"
            class="form-control form-control-sm"
            style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1);color:#e2e8f0">
    </div>
    @foreach(['' => 'Semua', 'admin' => 'Admin', 'affiliate' => 'Affiliate', 'customer' => 'Customer'] as $val => $label)
        <a href="{{ route('admin.users') }}?role={{ $val }}&search={{ request('search') }}"
            class="btn btn-sm {{ request('role', '') === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }}
        </a>
    @endforeach
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Telegram</th>
                    <th>Pesanan</th>
                    <th>Terdaftar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                @php
                    $roleStyle = match($user->role) {
                        'admin'     => ['rgba(248,113,113,0.15)', '#f87171'],
                        'affiliate' => ['rgba(167,139,250,0.15)', '#a78bfa'],
                        default     => ['rgba(107,114,128,0.15)', '#9ca3af'],
                    };
                @endphp
                <tr>
                    <td class="text-muted small">{{ $user->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $user->name }}</div>
                        @if($user->affiliateProfile)
                            <small style="color:#a78bfa"><i class="bi bi-link-45deg"></i>{{ $user->affiliateProfile->referral_code }}</small>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $user->email }}</td>
                    <td>
                        <span class="badge" style="background:{{ $roleStyle[0] }};color:{{ $roleStyle[1] }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td>
                        @if($user->telegram_chat_id)
                            <span class="badge" style="background:rgba(34,211,238,0.15);color:#22d3ee;font-size:.7rem">
                                <i class="bi bi-telegram me-1"></i>Terhubung
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="fw-semibold small">{{ $user->orders_count }}</td>
                    <td class="text-muted small" style="white-space:nowrap">
                        {{ $user->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y') }}
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge" style="background:rgba(16,185,129,0.15);color:#34d399">Aktif</span>
                        @else
                            <span class="badge" style="background:rgba(230,57,70,0.15);color:#ff6b7a">Nonaktif</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-3 d-block mb-2 opacity-50"></i>
                        Tidak ada pengguna ditemukan.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="card-footer d-flex justify-content-center py-2">
            <div class="d-flex align-items-center gap-3">
                @if($users->onFirstPage())
                    <span class="btn btn-sm btn-outline-secondary disabled">‹ Sebelumnya</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}&role={{ request('role') }}&search={{ request('search') }}" class="btn btn-sm btn-outline-secondary">‹ Sebelumnya</a>
                @endif
                <span class="text-muted small">{{ $users->currentPage() }} / {{ $users->lastPage() }}</span>
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}&role={{ request('role') }}&search={{ request('search') }}" class="btn btn-sm btn-outline-secondary">Berikutnya ›</a>
                @else
                    <span class="btn btn-sm btn-outline-secondary disabled">Berikutnya ›</span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
