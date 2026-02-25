@extends('layouts.app')
@section('title', 'Histori Pesanan — TDR HPZ')

@push('styles')
<style>
    .tdr-card {
        background: var(--tdr-card-bg);
        border: 1px solid var(--tdr-border);
        border-radius: 12px;
        padding: 20px 24px;
        transition: border-color .2s;
    }
    .tdr-card:hover { border-color: rgba(255,255,255,.18); }

    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600;
    }
    .status-pending    { background:rgba(255,193,7,.15);  color:#ffc107; }
    .status-verified   { background:rgba(13,202,240,.15); color:#0dcaf0; }
    .status-processing { background:rgba(13,110,253,.15); color:#6ea8fe; }
    .status-shipped    { background:rgba(25,135,84,.15);  color:#5dd39e; }
    .status-completed  { background:rgba(25,135,84,.25);  color:#5dd39e; }
    .status-cancelled  { background:rgba(230,57,70,.15);  color:var(--tdr-red); }

    .order-row { text-decoration: none !important; color: inherit !important; display: block; }
    .order-row:hover .tdr-card { border-color: var(--tdr-red); }
</style>
@endpush

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center gap-3 mb-4">
        <i class="bi bi-clock-history" style="font-size:1.6rem;color:var(--tdr-gold)"></i>
        <div>
            <h4 class="fw-bold mb-0">Histori Pesanan</h4>
            <p class="text-muted mb-0" style="font-size:.85rem">Semua pesanan yang pernah kamu buat</p>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="tdr-card text-center py-5">
            <i class="bi bi-inbox" style="font-size:3rem;color:var(--tdr-muted);display:block;margin-bottom:12px"></i>
            <div class="fw-semibold mb-1">Belum ada pesanan</div>
            <p class="text-muted" style="font-size:.875rem">Yuk mulai belanja di toko kami!</p>
            <a href="{{ route('shop') }}" class="btn btn-sm" style="background:var(--tdr-red);color:#fff;border-radius:8px">
                <i class="bi bi-bag me-1"></i>Lihat Produk
            </a>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($orders as $order)
            @php
                $statusMap = [
                    'pending'    => ['label' => 'Menunggu Pembayaran', 'class' => 'status-pending',    'icon' => 'bi-clock'],
                    'verified'   => ['label' => 'Pembayaran Dikonfirmasi','class' => 'status-verified', 'icon' => 'bi-check-circle'],
                    'processing' => ['label' => 'Sedang Diproses',     'class' => 'status-processing', 'icon' => 'bi-gear'],
                    'shipped'    => ['label' => 'Dikirim',             'class' => 'status-shipped',    'icon' => 'bi-truck'],
                    'completed'  => ['label' => 'Selesai',             'class' => 'status-completed',  'icon' => 'bi-bag-check'],
                    'cancelled'  => ['label' => 'Dibatalkan',          'class' => 'status-cancelled',  'icon' => 'bi-x-circle'],
                ];
                $s = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'status-pending', 'icon' => 'bi-circle'];
                $latestLog = $order->trackingLogs->first();
            @endphp
            <a href="{{ route('orders.track', $order->order_number) }}" class="order-row">
                <div class="tdr-card">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                        {{-- Left: Order info --}}
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:44px;height:44px;border-radius:10px;background:rgba(212,168,67,.1);border:1px solid rgba(212,168,67,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="bi bi-receipt" style="font-size:1.1rem;color:var(--tdr-gold)"></i>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:.9rem">{{ $order->order_number }}</div>
                                <div style="font-size:.75rem;color:var(--tdr-muted)">
                                    {{ $order->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                </div>
                                @if($order->items->isNotEmpty())
                                    <div style="font-size:.75rem;color:var(--tdr-muted);margin-top:2px">
                                        {{ $order->items->first()->product_name }}
                                        @if($order->items->count() > 1)
                                            <span>+{{ $order->items->count() - 1 }} item lainnya</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Right: Status + Total --}}
                        <div class="text-end">
                            <div class="mb-1">
                                <span class="status-badge {{ $s['class'] }}">
                                    <i class="bi {{ $s['icon'] }}"></i>{{ $s['label'] }}
                                </span>
                            </div>
                            <div class="fw-bold" style="color:var(--tdr-gold);font-size:.9rem">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </div>
                            @if($latestLog)
                                <div style="font-size:.7rem;color:var(--tdr-muted);margin-top:3px">
                                    {{ $latestLog->status_title }}
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
