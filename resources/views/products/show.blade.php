@extends('layouts.app')
@section('title', $product->name . ' — TDR HPZ')

@push('styles')
<style>
.spec-table td:first-child { color: var(--tdr-muted); width: 40%; font-size: .85rem; }
.spec-table td:last-child  { font-weight: 600; font-size: .85rem; }
.share-btn { border: 1px solid var(--tdr-border); background: transparent; color: var(--tdr-text); border-radius: 8px; padding: 6px 14px; font-size: .82rem; transition: all .15s; }
.share-btn:hover { border-color: var(--tdr-gold); color: var(--tdr-gold); }
</style>
@endpush

@section('content')
<div class="container py-5">

    @if(session('cart_success'))
        <div class="alert alert-success py-2 mb-3">
            <i class="bi bi-cart-check me-1"></i> {{ session('cart_success') }}
        </div>
    @endif

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size:.82rem">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop') }}" class="text-muted">Toko</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    {{-- Affiliate banner --}}
    @if($affiliate)
    <div class="alert py-2 mb-4 d-flex align-items-center gap-2"
         style="background:rgba(212,168,67,.1);border:1px solid rgba(212,168,67,.25);color:var(--tdr-gold);border-radius:10px">
        <i class="bi bi-tag-fill"></i>
        <span>Link referral dari <strong>{{ $affiliate->user?->name ?? $affCode }}</strong> aktif. Komisi otomatis tercatat saat checkout.</span>
    </div>
    @endif

    <div class="row g-5">

        {{-- Gambar --}}
        <div class="col-md-5">
            <div class="card d-flex align-items-center justify-content-center"
                 style="min-height:320px;background:rgba(255,255,255,.03)">
                @if($product->thumbnail_url)
                    <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}"
                         class="img-fluid rounded" style="max-height:340px;object-fit:contain">
                @else
                    <div style="font-size:5rem;color:var(--tdr-muted)"><i class="bi bi-box-seam"></i></div>
                @endif
            </div>

            {{-- Video --}}
            @if($product->master_video_url)
            <div class="mt-3">
                <div class="ratio ratio-16x9">
                    <iframe src="{{ $product->master_video_url }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
            @endif
        </div>

        {{-- Detail --}}
        <div class="col-md-7">
            @if($product->brand)
                <div style="font-size:.75rem;color:var(--tdr-muted);text-transform:uppercase;letter-spacing:1px" class="mb-1">{{ $product->brand }}</div>
            @endif
            <h2 class="fw-bold mb-2">{{ $product->name }}</h2>

            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="fs-3 fw-bold" style="color:var(--tdr-red)">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                @if($product->stock !== null)
                    @if($product->stock > 10)
                        <span class="badge bg-success">Stok tersedia</span>
                    @elseif($product->stock > 0)
                        <span class="badge bg-warning text-dark">Sisa {{ $product->stock }} pcs</span>
                    @else
                        <span class="badge bg-secondary">Stok habis</span>
                    @endif
                @endif
            </div>

            @if($product->description)
            <p class="text-muted mb-4" style="font-size:.9rem;line-height:1.7">{{ $product->description }}</p>
            @endif

            {{-- Specs --}}
            @if($product->technical_specs)
            <div class="mb-4">
                <div class="fw-semibold mb-2" style="font-size:.85rem">Spesifikasi</div>
                <table class="table table-sm spec-table">
                    @foreach(explode("\n", $product->technical_specs) as $line)
                        @if(str_contains($line, ':'))
                            @php [$k, $v] = explode(':', $line, 2); @endphp
                            <tr><td>{{ trim($k) }}</td><td>{{ trim($v) }}</td></tr>
                        @endif
                    @endforeach
                </table>
            </div>
            @endif

            {{-- Actions --}}
            @if(($product->stock ?? 1) > 0)
            <div class="d-flex flex-wrap gap-2 mb-4">

                {{-- Add to Cart --}}
                @auth
                <form method="POST" action="{{ route('cart.add') }}" class="d-flex gap-2 align-items-center">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="affiliate_code" value="{{ $affCode ?? '' }}">
                    <div class="input-group" style="width:110px">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnMinus">−</button>
                        <input type="number" name="quantity" id="qtyInput" value="1" min="1"
                               max="{{ $product->stock ?? 99 }}" class="form-control form-control-sm text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnPlus">+</button>
                    </div>
                    <button type="submit" class="btn fw-semibold"
                            style="background:rgba(255,255,255,.08);border:1px solid var(--tdr-border);color:var(--tdr-text);border-radius:8px">
                        <i class="bi bi-cart-plus me-1"></i>Keranjang
                    </button>
                </form>

                {{-- Direct Buy --}}
                <a href="{{ route('checkout.form') }}?product_id={{ $product->id }}{{ $affCode ? '&affiliate_code='.$affCode : '' }}"
                   class="btn btn-primary fw-semibold px-4">
                    <i class="bi bi-lightning-charge me-1"></i>Beli Sekarang
                </a>
                @else
                <a href="{{ route('login') }}" class="btn btn-primary fw-semibold px-4">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login untuk Beli
                </a>
                @endauth
            </div>
            @else
            <button class="btn btn-secondary fw-semibold px-4 mb-4" disabled>Stok Habis</button>
            @endif

            {{-- Share button --}}
            @php
                $shareLink = url('/products/' . $product->slug);
                if (auth()->check() && auth()->user()->affiliateProfile?->status === 'active') {
                    $shareLink .= '?ref=' . auth()->user()->affiliateProfile->referral_code;
                }
            @endphp
            <div class="mt-2">
                <button type="button" class="share-btn" onclick="copyShare('{{ $shareLink }}', this)">
                    <i class="bi bi-link-45deg me-1"></i>Salin Link Produk
                </button>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Qty stepper
document.getElementById('btnMinus')?.addEventListener('click', function () {
    const i = document.getElementById('qtyInput');
    if (parseInt(i.value) > 1) i.value = parseInt(i.value) - 1;
});
document.getElementById('btnPlus')?.addEventListener('click', function () {
    const i = document.getElementById('qtyInput');
    const max = parseInt(i.max) || 99;
    if (parseInt(i.value) < max) i.value = parseInt(i.value) + 1;
});

function copyShare(url, btn) {
    navigator.clipboard.writeText(url).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Disalin!';
        setTimeout(() => btn.innerHTML = orig, 2000);
    });
}

function copyCode(code, btn) {
    navigator.clipboard.writeText(code).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Disalin!';
        setTimeout(() => btn.innerHTML = orig, 2000);
    });
}
</script>
@endpush
