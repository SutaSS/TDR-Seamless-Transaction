@extends('layouts.app')
@section('title', 'Beranda - TDR HPZ Store')

@section('content')
{{-- Hero + Search --}}
<div class="hero-section mb-0">
    <div class="container text-center">
        <h1 class="fw-bold display-5 mb-2">🏍 TDR HPZ Store</h1>
        <p class="lead mb-4 opacity-75">Spare Part Motor Berkualitas · Harga Terbaik · Pengiriman Cepat</p>

        {{-- Search Bar --}}
        <form action="{{ route('home') }}" method="GET" class="row justify-content-center g-2">
            <div class="col-md-6">
                <div class="input-group input-group-lg shadow">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           class="form-control border-0"
                           placeholder="Cari produk, spare part...">
                    <button class="btn btn-warning fw-semibold px-4" type="submit">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Products Section --}}
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            @if(!empty($search))
                Hasil pencarian: <span class="text-primary">"{{ $search }}"</span>
            @else
                Produk Unggulan
            @endif
        </h3>
        @if(!empty($search))
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Hapus filter
            </a>
        @endif
    </div>

    @if($products->isEmpty())
        <div class="text-center py-5">
            <div class="display-1 mb-3">🔍</div>
            <h5 class="text-muted">Produk tidak ditemukan untuk "{{ $search }}"</h5>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">Lihat semua produk</a>
        </div>
    @else
    <div class="row g-4">
        @foreach($products as $product)
        <div class="col-sm-6 col-md-4">
            <div class="card h-100 shadow-sm">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" class="card-img-top" style="height:200px;object-fit:cover" alt="{{ $product->name }}">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;font-size:3rem">🏍</div>
                @endif
                <div class="card-body">
                    <h5 class="card-title fw-bold">{{ $product->name }}</h5>
                    @if($product->description)
                        <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit($product->description, 100) }}</p>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fs-5 fw-bold text-danger">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        @if($product->stock !== null)
                            <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-secondary' }}">
                                {{ $product->stock > 0 ? 'Stok: '.$product->stock : 'Habis' }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-3">
                    @auth
                        <a href="{{ route('checkout.form') }}" class="btn btn-primary w-100">
                            <i class="bi bi-cart-plus"></i> Beli Sekarang
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Login untuk Beli
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- CTA Affiliate --}}
    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
            <div class="card bg-dark text-white p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold">💰 Program Affiliate TDR HPZ</h4>
                        <p class="mb-0 opacity-75">Daftar gratis. Bagikan link. Dapatkan <strong>komisi 10%</strong> dari setiap pembelian — otomatis, real-time via Telegram.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        @auth
                            <a href="{{ route('affiliate.register.form') }}" class="btn btn-warning fw-semibold">Jadi Affiliate</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-warning fw-semibold">Daftar & Jadi Affiliate</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
