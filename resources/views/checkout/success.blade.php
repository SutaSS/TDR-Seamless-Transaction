@extends('layouts.app')
@section('title', 'Pembayaran Berhasil — TDR HPZ')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm p-5">
                <div class="mb-3 text-success" style="font-size:4rem">✅</div>
                <h4 class="fw-bold">Pembayaran Berhasil!</h4>
                <p class="text-muted">Terima kasih sudah berbelanja di TDR HPZ.</p>

                @if($order)
                <div class="alert alert-light text-start mt-3">
                    <div><strong>No. Pesanan:</strong> #{{ $order->order_number }}</div>
                    <div><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                    <div><strong>Status:</strong>
                        <span class="badge bg-primary">{{ $order->status }}</span>
                    </div>
                </div>
                <div class="alert alert-info text-start">
                    <i class="bi bi-telegram"></i> Notifikasi otomatis akan dikirim ke Telegram Anda
                    saat status pesanan diperbarui.
                </div>
                @endif

                <a href="/" class="btn btn-primary mt-2">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>
@endsection
