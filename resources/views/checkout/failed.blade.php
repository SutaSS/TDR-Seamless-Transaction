@extends('layouts.app')
@section('title', 'Pembayaran Gagal — TDR HPZ')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm p-5">
                <div class="mb-3 text-danger" style="font-size:4rem">❌</div>
                <h4 class="fw-bold">Pembayaran Dibatalkan</h4>
                <p class="text-muted">Pesanan Anda belum diproses. Silakan coba lagi.</p>
                <a href="/checkout" class="btn btn-primary mt-2">Coba Lagi</a>
                <a href="/" class="btn btn-outline-secondary mt-2 ms-2">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>
@endsection
