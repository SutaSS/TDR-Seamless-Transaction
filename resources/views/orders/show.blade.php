@extends('layouts.app')
@section('title', 'Detail Pesanan #'.$order->order_number)

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0">Pesanan #{{ $order->order_number }}</h4>
        <span class="badge badge-{{ $order->order_status }} fs-6">{{ $order->order_status }}</span>
    </div>

    <div class="row g-4">
        {{-- Left: items + timeline --}}
        <div class="col-md-8">
            {{-- Items --}}
            <div class="card mb-4">
                <div class="card-header fw-semibold">Item Pesanan</div>
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name_snapshot }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">Total</td>
                            <td class="text-end">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Order timeline --}}
            <div class="card">
                <div class="card-header fw-semibold">Status Pengiriman</div>
                <div class="card-body">
                    @php
                        $steps = [
                            ['status' => 'pending',    'icon' => 'clock',          'label' => 'Pesanan Diterima',    'desc' => 'Menunggu konfirmasi pembayaran'],
                            ['status' => 'processing', 'icon' => 'box-seam',       'label' => 'Diproses',            'desc' => 'Pesanan sedang dikemas'],
                            ['status' => 'shipped',    'icon' => 'truck',          'label' => 'Dikirim',             'desc' => 'Dalam perjalanan ke tujuan'],
                            ['status' => 'delivered',  'icon' => 'check-circle',   'label' => 'Selesai',             'desc' => 'Pesanan telah diterima'],
                        ];
                        $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
                        $currentIdx  = array_search($order->order_status, $statusOrder);
                        if ($order->order_status === 'cancelled') $currentIdx = -1;
                    @endphp

                    @if($order->order_status === 'cancelled')
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle me-2"></i>Pesanan ini dibatalkan.
                        </div>
                    @else
                        <div class="d-flex justify-content-between position-relative" style="padding-top:8px;">
                            {{-- Connector line --}}
                            <div style="position:absolute;top:28px;left:10%;right:10%;height:3px;background:#dee2e6;z-index:0;">
                                @php $pct = $currentIdx >= 0 ? ($currentIdx / 3 * 100) : 0; @endphp
                                <div style="width:{{ $pct }}%;height:100%;background:#0d6efd;transition:width .5s;"></div>
                            </div>

                            @foreach($steps as $i => $step)
                                @php
                                    $done    = $currentIdx !== false && $i <= $currentIdx;
                                    $current = $currentIdx !== false && $i === $currentIdx;
                                @endphp
                                <div class="text-center position-relative" style="z-index:1;flex:1;">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-2"
                                         style="width:40px;height:40px;
                                                background:{{ $done ? '#0d6efd' : '#dee2e6' }};
                                                color:{{ $done ? '#fff' : '#6c757d' }};
                                                border:3px solid {{ $current ? '#0a58ca' : 'transparent' }};">
                                        <i class="bi bi-{{ $step['icon'] }}"></i>
                                    </div>
                                    <div class="small fw-semibold {{ $done ? 'text-primary' : 'text-muted' }}">{{ $step['label'] }}</div>
                                    <div class="text-muted" style="font-size:.7rem;">{{ $step['desc'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: resi + payment info --}}
        <div class="col-md-4">
            {{-- Tracking / Resi --}}
            @if($order->tracking_number)
                <div class="card mb-3 border-success">
                    <div class="card-header bg-success text-white fw-semibold">
                        <i class="bi bi-truck me-2"></i>Info Pengiriman
                    </div>
                    <div class="card-body">
                        <div class="mb-1"><strong>Ekspedisi:</strong> {{ $order->shipping_provider ?? '-' }}</div>
                        <div class="mb-3">
                            <strong>No Resi:</strong>
                            <span class="fw-bold font-monospace fs-6 ms-1">{{ $order->tracking_number }}</span>
                            <button class="btn btn-sm btn-outline-secondary ms-2 py-0"
                                onclick="navigator.clipboard.writeText('{{ $order->tracking_number }}');this.textContent='✓ Disalin!';setTimeout(()=>this.textContent='Salin',1500)">
                                Salin
                            </button>
                        </div>
                        @php
                            $provider = strtolower($order->shipping_provider ?? '');
                            $trackUrl = null;
                            if (str_contains($provider, 'jne'))      $trackUrl = "https://www.jne.co.id/id/tracking/trace?awbNumber={$order->tracking_number}";
                            elseif (str_contains($provider, 'j&t') || str_contains($provider, 'jnt')) $trackUrl = "https://jet.co.id/track/{$order->tracking_number}";
                            elseif (str_contains($provider, 'sicepat')) $trackUrl = "https://www.sicepat.com/checkAwb?awb={$order->tracking_number}";
                            elseif (str_contains($provider, 'anteraja')) $trackUrl = "https://anteraja.id/tracking/{$order->tracking_number}";
                            elseif (str_contains($provider, 'pos'))   $trackUrl = "https://www.posindonesia.co.id/id/tracking?awb={$order->tracking_number}";
                        @endphp
                        @if($trackUrl)
                            <a href="{{ $trackUrl }}" target="_blank" class="btn btn-success btn-sm w-100">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Lacak Paket
                            </a>
                        @else
                            <a href="https://cekresi.com/?noresi={{ $order->tracking_number }}" target="_blank" class="btn btn-outline-success btn-sm w-100">
                                <i class="bi bi-search me-1"></i>Cek Resi Online
                            </a>
                        @endif
                    </div>
                </div>
            @elseif(in_array($order->order_status, ['shipped']))
                <div class="card mb-3 border-info">
                    <div class="card-body small text-center py-3">
                        <i class="bi bi-truck display-6 text-info"></i>
                        <p class="mt-2 mb-0">Paket sedang dalam perjalanan. Nomor resi akan segera tersedia.</p>
                    </div>
                </div>
            @endif

            {{-- Payment info --}}
            <div class="card mb-3">
                <div class="card-header fw-semibold">Pembayaran</div>
                <div class="card-body small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Status</span>
                        @php $payBadge = $order->payment_status === 'paid' ? 'success' : 'warning text-dark'; @endphp
                        <span class="badge bg-{{ $payBadge }}">{{ $order->payment_status }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Total</span>
                        <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                    </div>
                    @if($order->paid_at)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Waktu Bayar</span>
                            <span>{{ $order->paid_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($order->payment)
                        <div class="d-flex justify-content-between mt-1">
                            <span class="text-muted">Metode</span>
                            <span>{{ $order->payment->payment_method ?? '-' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Order info --}}
            <div class="card">
                <div class="card-header fw-semibold">Info Pesanan</div>
                <div class="card-body small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">No. Pesanan</span>
                        <span class="font-monospace">{{ $order->order_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tanggal</span>
                        <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($order->delivered_at)
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Diterima</span>
                            <span>{{ $order->delivered_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status Order</span>
                        <span class="badge badge-{{ $order->order_status }}">{{ $order->order_status }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
