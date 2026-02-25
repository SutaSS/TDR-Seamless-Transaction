@extends('layouts.admin')
@section('title', 'Detail Pesanan #'.$order->order_number)

@section('content')
<div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0">Pesanan #{{ $order->order_number }}</h4>
    <span class="badge fs-6 badge-{{ $order->status }}">{{ $order->status }}</span>
</div>

<div class="row g-4">
    {{-- Left column --}}
    <div class="col-md-8">
        {{-- Items --}}
        <div class="card mb-4">
            <div class="card-header"><strong>Item Pesanan</strong></div>
            <table class="table mb-0">
                <thead>
                    <tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th><th>Affiliate</th></tr>
                </thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->product_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        <td>
                            @if($item->affiliate_code)
                                <span class="badge" style="background:rgba(212,168,67,.15);color:var(--tdr-gold,#d4a843)">
                                    <i class="bi bi-tag me-1"></i>{{ $item->affiliate_code }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
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
            @forelse($order->trackingLogs as $h)
                <li class="list-group-item">
                    <span class="badge badge-paid">{{ $h->status_title }}</span>
                    <span class="text-muted small ms-2">{{ $h->created_at?->format('d/m/Y H:i') }}</span>
                    @if($h->description) <div class="small text-muted mt-1">{{ $h->description }}</div> @endif
                </li>
            @empty
                <li class="list-group-item text-muted">Belum ada riwayat</li>
            @endforelse
            </ul>
        </div>

        {{-- Update Status Form --}}
        @php
            $nextStatus = match($order->status) {
                'processing' => 'shipped',
                'shipped'    => 'completed',
                default      => null,
            };
        @endphp

        {{-- Waiting for Payment --}}
        @if(!$order->payment_verified_at)
        <div class="card mt-3" style="border-left:3px solid #fbbf24">
            <div class="card-header"><strong><i class="bi bi-hourglass-split me-1"></i> Menunggu Pembayaran</strong></div>
            <div class="card-body small">
                <p class="mb-2">Pembayaran akan dikonfirmasi otomatis oleh <strong>Midtrans webhook</strong> setelah pelanggan selesai bayar.</p>
                <p class="mb-0 text-muted">Alur: Snap payment > webhook <code>settlement</code> > status otomatis <em>paid</em> > notifikasi Telegram terkirim.</p>
                @if(!app()->isProduction())
                <hr>
                <p class="fw-semibold mb-2" style="color:var(--adm-red)"><i class="bi bi-flask me-1"></i> Mode Local — Simulasi Pembayaran</p>
                <form method="POST" action="{{ route('admin.orders.simulate-payment', $order) }}">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm"
                        onclick="return confirm('Simulasi pembayaran selesai untuk order ini?')">
                        <i class="bi bi-lightning-fill me-1"></i> Simulasi Webhook Settlement
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

        {{-- Update Status --}}
        @if($nextStatus)
        @php
            $courierLabels = [
                'jne_reg'   => 'JNE Reguler',
                'jne_yes'   => 'JNE YES',
                'jnt_reg'   => 'J&T Reguler',
                'sicepat'   => 'SiCepat',
                'pos_biasa' => 'Pos Indonesia',
            ];
            $courierDisplay = $courierLabels[$order->shipping_courier] ?? strtoupper($order->shipping_courier ?? '-');
        @endphp
        <div class="card mt-3">
            <div class="card-header"><strong>Update Status > {{ ucfirst($nextStatus) }}</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                    @if($nextStatus === 'shipped')
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor Resi <span class="text-muted fw-normal">(opsional)</span></label>
                                <input type="text" name="shipping_tracking_number" class="form-control" placeholder="JNE123456789">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ekspedisi</label>
                                <input type="text" class="form-control" value="{{ $courierDisplay }}" readonly
                                       style="background:rgba(255,255,255,.04);cursor:default">
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

        {{-- Manual Notification --}}
        @if($order->customer?->telegram_chat_id)
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Kirim Notifikasi Telegram</strong>
                <span class="badge" style="background:rgba(16,185,129,0.15);color:#34d399">Chat ID: {{ $order->customer->telegram_chat_id }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.notify', $order) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pilih Event</label>
                        <select name="event" class="form-select">
                            <option value="payment.confirmed">Pembayaran Dikonfirmasi</option>
                            <option value="order.processing">Sedang Diproses</option>
                            <option value="order.shipped">Pesanan Dikirim</option>
                            <option value="order.delivered">Pesanan Diterima</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-send me-1"></i> Kirim Sekarang
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="alert alert-warning mt-3 small">
            <i class="bi bi-exclamation-triangle me-1"></i> Pelanggan belum mengisi <strong>Telegram Chat ID</strong> di profil. Notifikasi tidak bisa dikirim.
        </div>
        @endif
    </div>

    {{-- Right column --}}
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header"><strong>Info Pelanggan</strong></div>
            <div class="card-body small">
                <div class="mb-1"><span class="text-muted">Nama:</span> {{ $order->customer?->name ?? '-' }}</div>
                <div><span class="text-muted">Email:</span> {{ $order->customer?->email ?? '-' }}</div>
            </div>
        </div>

        @if($order->affiliate)
        <div class="card mb-3">
            <div class="card-header"><strong>Affiliate</strong></div>
            <div class="card-body small">
                <div class="mb-1"><span class="text-muted">Kode:</span> {{ $order->affiliate?->affiliateProfile?->referral_code ?? '-' }}</div>
                <div><span class="text-muted">Nama:</span> {{ $order->affiliate?->name ?? '-' }}</div>
                @if($order->commission)
                    <div class="mt-2 fw-semibold" style="color:#5dd39e">
                        Komisi: Rp {{ number_format($order->commission->amount, 0, ',', '.') }}
                        ({{ $order->commission->commission_rate }}%)
                    </div>
                @endif
            </div>
        </div>
        @endif

        @if($order->payment_verified_at)
        <div class="card mb-3">
            <div class="card-header"><strong>Pembayaran</strong></div>
            <div class="card-body small">
                <div class="mb-1"><span class="text-muted">Metode:</span> {{ $order->payment_method ?? '-' }}</div>
                <div class="mb-1"><span class="text-muted">Status:</span> <span class="badge" style="background:rgba(16,185,129,0.15);color:#34d399">Lunas</span></div>
                <div><span class="text-muted">Waktu:</span> {{ $order->payment_verified_at?->format('d/m/Y H:i') ?? '-' }}</div>
                @if($order->midtrans_transaction_id)
                    <div class="mt-1"><span class="text-muted">ID Transaksi:</span> <code>{{ $order->midtrans_transaction_id }}</code></div>
                @endif
            </div>
        </div>
        @endif

        @if($order->shipping_tracking_number)
        <div class="card">
            <div class="card-header"><strong>Pengiriman</strong></div>
            <div class="card-body small">
                <div class="mb-1"><span class="text-muted">Ekspedisi:</span> {{ $order->shipping_courier ?? '-' }}</div>
                <div><span class="text-muted">No Resi:</span> <span class="fw-bold">{{ $order->shipping_tracking_number }}</span></div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
