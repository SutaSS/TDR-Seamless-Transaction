@extends('layouts.app')
@section('title', 'Katalog Produk - TDR HPZ')

@section('content')
{{-- Page Header --}}
<div style="background: linear-gradient(135deg, #1d3557 0%, #457b9d 100%); color:#fff; padding:40px 0 30px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1">🛒 Katalog Produk</h2>
                <p class="mb-0 opacity-75">Spare part motor berkualitas TDR & HPZ — harga terjangkau, stok selalu tersedia</p>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <form action="{{ route('shop') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ $search }}"
                               class="form-control border-0" placeholder="Cari produk, spare part...">
                        <button class="btn btn-warning fw-semibold px-4" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">

    {{-- Search result info --}}
    @if($search)
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0 text-muted">Hasil pencarian: <strong>"{{ $search }}"</strong> — {{ $products->total() }} produk ditemukan</p>
        <a href="{{ route('shop') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x-circle"></i> Hapus filter
        </a>
    </div>
    @endif

    @if($products->isEmpty())
        <div class="text-center py-5">
            <div class="display-1 mb-3">🔍</div>
            <h5 class="text-muted">Produk tidak ditemukan untuk "{{ $search }}"</h5>
            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">Lihat semua produk</a>
        </div>
    @else
    <div class="row g-4">
        @foreach($products as $product)
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border-0 product-card">
                @if($product->thumbnail_url ?? false)
                    <img src="{{ $product->thumbnail_url }}" class="card-img-top" style="height:180px;object-fit:cover" alt="{{ $product->name }}">
                @else
                    <div class="bg-light d-flex flex-column align-items-center justify-content-center" style="height:180px;">
                        <div style="font-size:3rem">🏍</div>
                        <small class="text-muted mt-1">{{ $product->sku ?? '' }}</small>
                    </div>
                @endif
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title fw-bold mb-1">{{ $product->name }}</h6>
                    @if($product->description)
                        <p class="card-text text-muted small flex-grow-1">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>
                    @else
                        <div class="flex-grow-1"></div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fs-6 fw-bold text-danger">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        @if($product->stock !== null)
                            <span class="badge {{ $product->stock > 5 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning text-dark' : 'bg-secondary') }}" style="font-size:.7rem">
                                {{ $product->stock > 0 ? 'Stok: '.$product->stock : 'Habis' }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-3">
                    @if(($product->stock ?? 1) > 0)
                        @auth
                            <a href="{{ route('checkout.form') }}?product_id={{ $product->id }}" class="btn btn-primary w-100 fw-semibold">
                                <i class="bi bi-cart-plus"></i> Beli Sekarang
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 fw-semibold">
                                <i class="bi bi-box-arrow-in-right"></i> Login untuk Beli
                            </a>
                        @endauth
                    @else
                        <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $products->appends(['search' => $search])->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
.product-card { transition: transform .15s ease, box-shadow .15s ease; }
.product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.12) !important; }
</style>
@endpush
@endsection
