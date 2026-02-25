@extends('layouts.admin')
@section('title', 'Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Daftar Pesanan</h4>
    <form class="d-flex gap-2" method="GET">
        <select name="status" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach(['pending','processing','shipped','delivered','cancelled'] as $st)
                <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Affiliate</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th>Waktu</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold">#{{ $order->order_number }}</a></td>
                    <td>{{ $order->customer?->name ?? '-' }}</td>
                    <td>
                        @if($order->affiliate)
                            <span class="badge" style="background:rgba(6,182,212,0.15);color:#22d3ee">{{ $order->affiliate->affiliateProfile?->referral_code ?? '-' }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge badge-{{ $order->status }}">{{ $order->status }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $order->payment_verified_at ? 'badge-paid' : 'badge-cancelled' }}">
                            {{ $order->payment_verified_at ? 'paid' : 'unpaid' }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-5">Belum ada pesanan</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $orders->appends(request()->query())->links() }}</div>
</div>
@endsection
