@extends('layouts.app')
@section('title', 'Pembayaran Gagal — TDR HPZ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm p-5">
                <div class="mb-3">
                    <div style="width:72px;height:72px;border-radius:50%;background:rgba(230,57,70,0.12);display:inline-flex;align-items:center;justify-content:center">
                        <i class="bi bi-x-lg" style="font-size:2rem;color:#ff6b7a"></i>
                    </div>
                </div>
                <h4 class="fw-bold">Pembayaran Dibatalkan</h4>
                <p class="text-muted">Pesanan Anda belum diproses. Silakan coba lagi.</p>
                <div class="d-flex gap-2 justify-content-center mt-2">
                    <a href="/checkout" class="btn btn-primary">Coba Lagi</a>
                    <a href="/" class="btn btn-outline-secondary">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
