@extends('layouts.app')
@section('title', 'Lacak Pesanan ' . $order->order_number . ' — TDR HPZ')

@push('styles')
<style>
    /* ── Timeline ─────────────────────────────────────────── */
    .timeline {
        position: relative;
        padding-left: 28px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 6px;
        bottom: 6px;
        width: 2px;
        background: var(--tdr-border);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 24px;
    }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-dot {
        position: absolute;
        left: -28px;
        top: 4px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .65rem;
        border: 2px solid var(--tdr-dark);
    }
    .timeline-dot.done   { background: #5dd39e; color: #0b0b0f; }
    .timeline-dot.latest { background: var(--tdr-gold); color: #0b0b0f; }
    .timeline-dot.pending { background: rgba(255,255,255,.15); color: var(--tdr-muted); }

    .timeline-title   { font-weight: 600; font-size: .9rem; margin-bottom: 3px; }
    .timeline-desc    { font-size: .8rem; color: var(--tdr-muted); margin-bottom: 4px; }
    .timeline-time    { font-size: .72rem; color: rgba(255,255,255,.3); }

    /* ── Status badge map ─────────────────────────────────── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 20px; font-size: .75rem; font-weight: 600;
    }
    .status-pending    { background:rgba(255,193,7,.15); color:#ffc107; }
    .status-verified   { background:rgba(13,202,240,.15); color:#0dcaf0; }
    .status-processing { background:rgba(13,110,253,.15); color:#6ea8fe; }
    .status-shipped    { background:rgba(25,135,84,.15); color:#5dd39e; }
    .status-completed  { background:rgba(25,135,84,.25); color:#5dd39e; }
    .status-cancelled  { background:rgba(230,57,70,.15); color:var(--tdr-red); }

    .tdr-card {
        background: var(--tdr-card-bg);
        border: 1px solid var(--tdr-border);
        border-radius: 12px;
        padding: 24px;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center g-4">

        {{-- ── Left: Order Info ── --}}
        <div class="col-lg-5 col-md-6">

            {{-- Header --}}
            <div class="tdr-card mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-receipt" style="font-size:1.4rem;color:var(--tdr-gold)"></i>
                    <div>
                        <div class="fw-bold" style="font-size:1rem">{{ $order->order_number }}</div>
                        <div style="font-size:.75rem;color:var(--tdr-muted)">
                            Dipesan: {{ $order->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                </div>

                <div class="mb-3">
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
                    @endphp
                    <span class="status-badge {{ $s['class'] }}">
                        <i class="bi {{ $s['icon'] }}"></i>
                        {{ $s['label'] }}
                    </span>
                </div>

                <hr style="border-color:var(--tdr-border);opacity:1;margin:12px 0">

                <div class="d-flex justify-content-between mb-2" style="font-size:.875rem">
                    <span style="color:var(--tdr-muted)">Metode Pembayaran</span>
                    <span class="fw-medium">{{ $order->payment_method ?? 'Midtrans' }}</span>
                </div>
                @if($order->payment_verified_at)
                <div class="d-flex justify-content-between mb-2" style="font-size:.875rem">
                    <span style="color:var(--tdr-muted)">Dikonfirmasi</span>
                    <span class="fw-medium">{{ $order->payment_verified_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}</span>
                </div>
                @endif
                @if($order->shipping_courier)
                <div class="d-flex justify-content-between mb-2" style="font-size:.875rem">
                    <span style="color:var(--tdr-muted)">Kurir</span>
                    <span class="fw-medium">{{ $order->shipping_courier }}</span>
                </div>
                @endif
                @if($order->shipping_tracking_number)
                <div class="d-flex justify-content-between mb-2" style="font-size:.875rem">
                    <span style="color:var(--tdr-muted)">Nomor Resi</span>
                    <code>{{ $order->shipping_tracking_number }}</code>
                </div>
                @endif

                <hr style="border-color:var(--tdr-border);opacity:1;margin:12px 0">

                {{-- Items --}}
                @foreach($order->items as $item)
                <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.875rem">
                    <div>
                        <span class="fw-medium">{{ $item->product?->name ?? 'Produk' }}</span>
                        <span style="color:var(--tdr-muted)"> × {{ $item->quantity }}</span>
                    </div>
                    <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
                @endforeach

                <hr style="border-color:var(--tdr-border);opacity:1;margin:12px 0">

                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span style="color:var(--tdr-gold)">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                @if($order->shipping_address)
                <hr style="border-color:var(--tdr-border);opacity:1;margin:12px 0">
                <div style="font-size:.8rem;color:var(--tdr-muted)">
                    <i class="bi bi-geo-alt me-1"></i>{{ $order->shipping_address }}
                </div>
                @endif
            </div>

            <a href="{{ route('shop') }}" class="btn btn-outline-light btn-sm w-100">
                <i class="bi bi-arrow-left me-1"></i>Belanja Lagi
            </a>
        </div>

        {{-- ── Right: Timeline ── --}}
        <div class="col-lg-5 col-md-6">
            <div class="tdr-card">
                <div class="fw-bold mb-4" style="font-size:1rem">
                    <i class="bi bi-map me-2" style="color:var(--tdr-gold)"></i>Riwayat Pesanan
                </div>

                @if($order->trackingLogs->isEmpty())
                    <div class="text-center py-4" style="color:var(--tdr-muted);font-size:.875rem">
                        <i class="bi bi-hourglass-split" style="font-size:2rem;display:block;margin-bottom:8px"></i>
                        Belum ada riwayat untuk pesanan ini.
                    </div>
                @else
                    <div class="timeline">
                        @foreach($order->trackingLogs as $i => $log)
                            @php
                                $isLatest  = $i === $order->trackingLogs->count() - 1;
                                $dotClass  = $isLatest ? 'latest' : 'done';
                                $dotIcon   = $isLatest ? 'bi-circle-fill' : 'bi-check-lg';
                            @endphp
                            <div class="timeline-item">
                                <span class="timeline-dot {{ $dotClass }}">
                                    <i class="bi {{ $dotIcon }}"></i>
                                </span>
                                <div class="timeline-title">{{ $log->status_title }}</div>
                                @if($log->description)
                                    <div class="timeline-desc">{{ $log->description }}</div>
                                @endif
                                <div class="timeline-time">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Telegram hint --}}
                @if(auth()->user()->telegram_chat_id)
                    <div class="mt-4 p-3 rounded-3" style="background:rgba(0,136,204,.08);border:1px solid rgba(0,136,204,.2);font-size:.8rem;color:#5eb8d4">
                        <i class="bi bi-telegram me-1"></i>
                        Notifikasi otomatis dikirim ke Telegram saat status berubah.
                    </div>
                @else
                    <div class="mt-4 p-3 rounded-3" style="background:rgba(255,193,7,.06);border:1px solid rgba(255,193,7,.2);font-size:.8rem;color:#ffc107">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Telegram belum terhubung.
                        <a href="https://t.me/{{ config('services.telegram.bot_username') }}" target="_blank" style="color:#ffc107;font-weight:600">
                            Hubungkan sekarang →
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
