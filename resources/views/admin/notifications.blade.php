@extends('layouts.admin')
@section('title', 'Log Notifikasi')

@section('content')
<h4 class="fw-bold mb-4">Log Notifikasi Telegram</h4>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Penerima</th>
                    <th>Pesanan</th>
                    <th>Tipe Pesan</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
            @forelse($notifications as $n)
                <tr>
                    <td class="text-muted small">#{{ $n->id }}</td>
                    <td>{{ $n->user?->name ?? $n->recipient ?? '--' }}</td>
                    <td>
                        @if($n->order)
                            <a href="{{ route('admin.orders.show', $n->order) }}">#{{ $n->order->order_number }}</a>
                        @else --
                        @endif
                    </td>
                    <td><span class="badge" style="background:rgba(107,114,128,0.15);color:#9ca3af">{{ $n->message_type }}</span></td>
                    <td>
                        @php
                            $nStyle = match($n->status) {
                                'sent'   => ['rgba(16,185,129,0.15)', '#34d399'],
                                'failed' => ['rgba(230,57,70,0.15)', '#ff6b7a'],
                                'queued' => ['rgba(245,158,11,0.15)', '#fbbf24'],
                                default  => ['rgba(107,114,128,0.15)', '#9ca3af'],
                            };
                        @endphp
                        <span class="badge" style="background:{{ $nStyle[0] }};color:{{ $nStyle[1] }}">{{ $n->status }}</span>
                    </td>
                    <td class="text-muted small">
                        {{ $n->sent_at?->format('d/m H:i') ?? $n->created_at->format('d/m H:i') }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">Belum ada notifikasi</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $notifications->links() }}</div>
</div>
@endsection
