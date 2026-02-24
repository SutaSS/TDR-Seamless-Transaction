@extends('layouts.admin')
@section('title', 'Detail Pesanan #'.$order->order_number)

@section('content')
<div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0">Pesanan #{{ $order->order_number }}</h4>
    <span class="badge fs-6 bg-{{ match($order->order_status) {
        'pending'=>'warning text-dark','paid'=>'primary','shipped'=>'info text-dark','delivered'=>'success',default=>'secondary'
    } }}">{{ $order->order_status }}</span>
</div>

<div class="row g-4">
    {{-- Left column --}}
    <div class="col-md-8">
        {{-- Items --}}
        <div class="card mb-4">
            <div class="card-header"><strong>Item Pesanan</strong></div>
            <table class="table mb-0">
                <thead class="table-light">
                    <tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name_snapshot }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">Total</td>
                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Status History --}}
        <div class="card mb-4">
            <div class="card-header"><strong>Riwayat Status</strong></div>
            <ul class="list-group list-group-flush">
            @forelse($order->statusHistories as $h)
                <li class="list-group-item">
                    <span class="badge bg-secondary">{{ $h->from_status ?? '—' }}</span>
                    <i class="bi bi-arrow-right mx-1"></i>
                    <span class="badge bg-primary">{{ $h->to_status }}</span>
                    <span class="text-muted small ms-2">oleh {{ $h->changedBy?->name ?? 'System' }} · {{ $h->changed_at?->format('d/m/Y H:i') }}</span>
                    @if($h->note) <div class="small text-muted mt-1">{{ $h->note }}</div> @endif
                </li>
            @empty
                <li class="list-group-item text-muted">Belum ada riwayat</li>
            @endforelse
            </ul>
        </div>

        {{-- Update Status Form --}}
        @php
            $transitions = ['pending'=>'paid','paid'=>'shipped','shipped'=>'delivered'];
            $nextStatus  = $transitions[$order->order_status] ?? null;
        @endphp
        @if($nextStatus)
        <div class="card">
            <div class="card-header"><strong>Update Status → {{ ucfirst($nextStatus) }}</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                    @if($nextStatus === 'shipped')
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor Resi <span class="text-danger">*</span></label>
                                <input type="text" name="tracking_number" class="form-control" placeholder="JNE123456789" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ekspedisi</label>
                                <input type="text" name="shipping_provider" class="form-control" placeholder="JNE / J&T / SiCepat">
                            </div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <input type="text" name="note" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        Ubah ke {{ ucfirst($nextStatus) }}
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Right column --}}
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header"><strong>Info Pelanggan</strong></div>
            <div class="card-body small">
                <div><strong>Nama:</strong> {{ $order->customer_name ?? $order->customer?->name ?? '-' }}</div>
                <div><strong>Telepon:</strong> {{ $order->customer_phone ?? '-' }}</div>
                <div><strong>Email:</strong> {{ $order->customer?->email ?? '-' }}</div>
            </div>
        </div>

        @if($order->affiliate)
        <div class="card mb-3">
            <div class="card-header"><strong>Affiliate</strong></div>
            <div class="card-body small">
                <div><strong>Kode:</strong> {{ $order->affiliate->referral_code }}</div>
                <div><strong>Nama:</strong> {{ $order->affiliate->user?->name ?? '-' }}</div>
                @if($order->conversion)
                    <div class="mt-2 text-success fw-semibold">
                        Komisi: Rp {{ number_format($order->conversion->commission_amount, 0, ',', '.') }}
                        ({{ $order->conversion->commission_rate }}%)
                    </div>
                @endif
            </div>
        </div>
        @endif

        @if($order->payment)
        <div class="card mb-3">
            <div class="card-header"><strong>Pembayaran</strong></div>
            <div class="card-body small">
                <div><strong>Gateway:</strong> {{ $order->payment->gateway_provider }}</div>
                <div><strong>Metode:</strong> {{ $order->payment->payment_method ?? '-' }}</div>
                <div><strong>Status:</strong> {{ $order->payment->status }}</div>
                <div><strong>Waktu:</strong> {{ $order->payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</div>
            </div>
        </div>
        @endif

        @if($order->tracking_number)
        <div class="card">
            <div class="card-header"><strong>Pengiriman</strong></div>
            <div class="card-body small">
                <div><strong>Ekspedisi:</strong> {{ $order->shipping_provider ?? '-' }}</div>
                <div><strong>No Resi:</strong> <span class="fw-bold">{{ $order->tracking_number }}</span></div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
