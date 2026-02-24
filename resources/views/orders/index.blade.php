@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4 gap-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-bag-check me-2 text-primary"></i>Pesanan Saya</h4>
        <span class="badge bg-secondary">{{ $orders->total() }} pesanan</span>
    </div>

    @if($orders->isEmpty())
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="bi bi-bag-x display-4 text-muted"></i>
                <p class="mt-3 text-muted">Kamu belum punya pesanan.</p>
                <a href="{{ route('shop') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-shop me-1"></i> Mulai Belanja
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Resi / Pengiriman</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="fw-semibold small">{{ $order->order_number }}</td>
                            <td class="small text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="small">
                                @foreach($order->items->take(2) as $item)
                                    <div>{{ $item->product_name_snapshot }}
                                        <span class="text-muted">×{{ $item->qty }}</span>
                                    </div>
                                @endforeach
                                @if($order->items->count() > 2)
                                    <div class="text-muted">+{{ $order->items->count() - 2 }} item lainnya</div>
                                @endif
                            </td>
                            <td class="fw-semibold small">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @php
                                    $payBadge = match($order->payment_status) {
                                        'paid'   => 'success',
                                        'unpaid' => 'warning text-dark',
                                        'failed' => 'danger',
                                        default  => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $payBadge }}">{{ $order->payment_status }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $order->order_status }}">{{ $order->order_status }}</span>
                            </td>
                            <td class="small">
                                @if($order->tracking_number)
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-truck text-success"></i>
                                        <div>
                                            <div class="fw-semibold">{{ $order->tracking_number }}</div>
                                            @if($order->shipping_provider)
                                                <div class="text-muted" style="font-size:.75rem">{{ $order->shipping_provider }}</div>
                                            @endif
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
                                                <a href="{{ $trackUrl }}" target="_blank" class="small text-primary" style="font-size:.72rem">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i>Lacak
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($order->order_status === 'shipped')
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i>Sedang dikirim</span>
                                @elseif(in_array($order->order_status, ['pending', 'processing']))
                                    <span class="text-muted small">—</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
