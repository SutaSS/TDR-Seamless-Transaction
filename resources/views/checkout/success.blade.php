@extends('layouts.app')
@section('title', 'Pembayaran Berhasil — TDR HPZ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm p-5">
                <div class="mb-3">
                    <div style="width:72px;height:72px;border-radius:50%;background:rgba(25,135,84,0.12);display:inline-flex;align-items:center;justify-content:center">
                        <i class="bi bi-check-lg" style="font-size:2rem;color:#5dd39e"></i>
                    </div>
                </div>
                <h4 class="fw-bold">Pembayaran Berhasil!</h4>
                <p class="text-muted">Terima kasih sudah berbelanja di TDR HPZ.</p>

                @if($order)
                <div class="text-start mt-3 p-3 rounded-3" style="background:rgba(255,255,255,0.04);border:1px solid var(--tdr-border)">
                    <div><strong>No. Pesanan:</strong> #{{ $order->order_number }}</div>
                    <div><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                    <div><strong>Status:</strong>
                        <span class="badge" style="background:rgba(230,57,70,0.15);color:var(--tdr-red)">{{ $order->status }}</span>
                    </div>
                </div>
                <div class="alert alert-info text-start mt-3 small">
                    <i class="bi bi-telegram me-1"></i> Notifikasi otomatis akan dikirim ke Telegram Anda
                    saat status pesanan diperbarui.
                </div>
                <a href="{{ route('orders.track', $order->order_number) }}"
                   class="btn w-100 mt-2 fw-semibold"
                   style="background:rgba(212,168,67,.15);color:var(--tdr-gold);border:1px solid rgba(212,168,67,.3);border-radius:10px">
                    <i class="bi bi-map me-2"></i>Lacak Pesanan Ini
                </a>
                @endif

                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-light btn-sm flex-fill">
                        <i class="bi bi-clock-history me-1"></i>Histori
                    </a>
                    <a href="/" class="btn btn-outline-light btn-sm flex-fill">
                        <i class="bi bi-house me-1"></i>Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
