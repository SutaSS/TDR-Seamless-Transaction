@extends('layouts.app')
@section('title', 'Katalog Produk - TDR HPZ')

@section('content')
{{-- Page Header --}}
<div style="background:linear-gradient(165deg,#0b0b0f 0%,#1a1a2e 50%,#16213e 100%);padding:40px 0 30px;border-bottom:1px solid var(--tdr-border)">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1"><i class="bi bi-grid-3x3-gap me-2" style="color:var(--tdr-red)"></i>Katalog Produk</h2>
                <p class="mb-0" style="color:var(--tdr-muted)">Spare part motor berkualitas TDR & HPZ — harga terjangkau, stok selalu tersedia</p>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <form action="{{ route('shop') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ $search }}"
                               class="form-control" placeholder="Cari produk, spare part..." style="border-right:none">
                        <button class="btn btn-primary fw-semibold px-4" type="submit">
                            <i class="bi bi-search me-1"></i> Cari
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
        <p class="mb-0 text-muted">Hasil pencarian: <strong style="color:var(--tdr-text)">"{{ $search }}"</strong> — {{ $products->total() }} produk ditemukan</p>
        <a href="{{ route('shop') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x-circle me-1"></i> Hapus filter
        </a>
    </div>
    @endif

    @if($products->isEmpty())
        <div class="text-center py-5">
            <div style="font-size:3rem;color:var(--tdr-muted)" class="mb-3"><i class="bi bi-search"></i></div>
            <h5 class="text-muted">Produk tidak ditemukan untuk "{{ $search }}"</h5>
            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">Lihat semua produk</a>
        </div>
    @else
    <div class="row g-4">
        @foreach($products as $product)
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 product-card"
                 style="cursor:pointer"
                 onclick="window.location='{{ route('product.show', $product->slug) }}'">
                <a href="{{ route('product.show', $product->slug) }}" style="text-decoration:none;display:block" onclick="event.stopPropagation()">
                @if($product->thumbnail_url ?? false)
                    <img src="{{ $product->thumbnail_url }}" class="card-img-top" style="height:180px;object-fit:cover;border-radius:12px 12px 0 0" alt="{{ $product->name }}">
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center" style="height:180px;background:rgba(255,255,255,0.03);border-radius:12px 12px 0 0">
                        <div style="font-size:2.5rem;color:var(--tdr-muted)"><i class="bi bi-box-seam"></i></div>
                        <small class="text-muted mt-1">{{ $product->sku ?? '' }}</small>
                    </div>
                @endif
                </a>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title fw-bold mb-1">
                        <a href="{{ route('product.show', $product->slug) }}" style="color:var(--tdr-text);text-decoration:none"
                           onclick="event.stopPropagation()">{{ $product->name }}</a>
                    </h6>
                    @if($product->description)
                        <p class="card-text text-muted small flex-grow-1">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>
                    @else
                        <div class="flex-grow-1"></div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fs-6 fw-bold" style="color:var(--tdr-red)">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        @if($product->stock !== null)
                            <span class="badge {{ $product->stock > 5 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning text-dark' : 'bg-secondary') }}" style="font-size:.7rem">
                                {{ $product->stock > 0 ? 'Stok: '.$product->stock : 'Habis' }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-footer border-0 pb-3">
                    @if(($product->stock ?? 1) > 0)
                        @auth
                        <form method="POST" action="{{ route('cart.add') }}" class="d-flex gap-2"
                              onclick="event.stopPropagation()">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            {{-- No affiliate_code here: referral only applies via product-specific share links --}}
                            <button type="submit" class="btn btn-primary flex-grow-1 fw-semibold" style="font-size:.8rem">
                                <i class="bi bi-cart-plus me-1"></i>Keranjang
                            </button>
                            <a href="{{ route('product.show', $product->slug) }}"
                               class="btn btn-outline-secondary" style="font-size:.8rem" title="Lihat Detail"
                               onclick="event.stopPropagation()">
                                <i class="bi bi-eye"></i>
                            </a>
                        </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 fw-semibold"
                               onclick="event.stopPropagation()">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Beli
                            </a>
                        @endauth
                    @else
                        <button class="btn btn-secondary w-100" disabled onclick="event.stopPropagation()">Stok Habis</button>
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
.product-card { transition: transform .2s ease, box-shadow .2s ease; }
.product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.3) !important; }
</style>
@endpush
@endsection
