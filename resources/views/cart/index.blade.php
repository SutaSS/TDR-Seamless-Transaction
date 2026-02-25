@extends('layouts.app')
@section('title', 'Keranjang Belanja — TDR HPZ')

@push('styles')
<style>
.cart-item-card {
    background: var(--tdr-card-bg);
    border: 1px solid var(--tdr-border);
    border-radius: 12px;
    padding: 16px 20px;
    transition: border-color .2s;
}
.cart-item-card:hover { border-color: rgba(255,255,255,.18); }
.aff-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 20px; font-size: .7rem; font-weight: 600;
    background: rgba(212,168,67,.12); color: var(--tdr-gold);
    border: 1px solid rgba(212,168,67,.2);
}
</style>
@endpush

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center gap-3 mb-4">
        <i class="bi bi-cart3" style="font-size:1.6rem;color:var(--tdr-gold)"></i>
        <div>
            <h4 class="fw-bold mb-0">Keranjang Belanja</h4>
            <p class="text-muted mb-0" style="font-size:.85rem">{{ count($cart) }} produk</p>
        </div>
    </div>

    @if(session('cart_success'))
        <div class="alert alert-success py-2 mb-3">
            <i class="bi bi-cart-check me-1"></i> {{ session('cart_success') }}
        </div>
    @endif

    @if(empty($cart))
        <div class="card text-center py-5">
            <i class="bi bi-cart-x" style="font-size:3.5rem;color:var(--tdr-muted);display:block;margin-bottom:16px"></i>
            <div class="fw-semibold mb-1">Keranjang masih kosong</div>
            <p class="text-muted mb-3" style="font-size:.875rem">Yuk tambahkan produk ke keranjang!</p>
            <div>
                <a href="{{ route('shop') }}" class="btn fw-semibold px-4"
                   style="background:var(--tdr-red);color:#fff;border-radius:8px">
                    <i class="bi bi-bag me-1"></i>Lihat Produk
                </a>
            </div>
        </div>
    @else
    <div class="row g-4 align-items-start">

        {{-- Left: Items --}}
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-3">
                @foreach($cart as $productId => $item)
                @php $itemTotal = $item['product_price'] * $item['quantity']; @endphp
                <div class="cart-item-card" id="cart-row-{{ $productId }}">
                    <div class="d-flex flex-wrap gap-3 align-items-center">

                        {{-- Thumbnail --}}
                        <div style="width:60px;height:60px;flex-shrink:0;border-radius:8px;overflow:hidden;background:rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center">
                            @if($item['thumbnail_url'] ?? null)
                                <img src="{{ $item['thumbnail_url'] }}" style="width:100%;height:100%;object-fit:cover" alt="">
                            @else
                                <i class="bi bi-box-seam" style="font-size:1.4rem;color:var(--tdr-muted)"></i>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold" style="font-size:.9rem">
                                <a href="{{ route('product.show', $item['product_slug'] ?? $productId) }}"
                                   class="text-decoration-none" style="color:var(--tdr-text)">
                                    {{ $item['product_name'] }}
                                </a>
                            </div>
                            <div class="text-muted" style="font-size:.78rem">
                                Rp {{ number_format($item['product_price'], 0, ',', '.') }} / pcs
                            </div>
                            @if(! empty($item['affiliate_code']))
                            <div class="mt-1">
                                <span class="aff-badge">
                                    <i class="bi bi-tag"></i>Ref: {{ $item['affiliate_code'] }}
                                </span>
                            </div>
                            @endif
                        </div>

                        {{-- Qty updater --}}
                        <form method="POST" action="{{ route('cart.update', $productId) }}" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <div class="input-group" style="width:110px">
                                <button type="button" class="btn btn-outline-secondary btn-sm qty-minus">−</button>
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                       max="{{ $item['stock'] ?? 99 }}"
                                       class="form-control form-control-sm text-center qty-input"
                                       onchange="this.form.submit()">
                                <button type="button" class="btn btn-outline-secondary btn-sm qty-plus">+</button>
                            </div>
                        </form>

                        {{-- Subtotal + Remove --}}
                        <div class="text-end" style="min-width:110px">
                            <div class="fw-bold" style="color:var(--tdr-gold);font-size:.95rem">
                                Rp {{ number_format($itemTotal, 0, ',', '.') }}
                            </div>
                            <form method="POST" action="{{ route('cart.remove', $productId) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link p-0 mt-1"
                                        style="color:var(--tdr-red);font-size:.75rem;text-decoration:none">
                                    <i class="bi bi-trash3"></i> Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="{{ route('shop') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Lanjut Belanja
                </a>
                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Kosongkan seluruh keranjang?')">
                        <i class="bi bi-trash3 me-1"></i>Kosongkan
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: Summary --}}
        <div class="col-lg-4">
            <div class="card sticky-top p-4" style="top:16px">
                <h6 class="fw-bold mb-3">Ringkasan</h6>

                @foreach($cart as $productId => $item)
                <div class="d-flex justify-content-between mb-1" style="font-size:.82rem">
                    <span class="text-muted text-truncate me-2" style="max-width:160px">{{ $item['product_name'] }}</span>
                    <span>Rp {{ number_format($item['product_price'] * $item['quantity'], 0, ',', '.') }}</span>
                </div>
                @endforeach

                <hr style="border-color:var(--tdr-border);opacity:1;margin:12px 0">

                @php $cartTotal = collect($cart)->sum(fn($i) => $i['product_price'] * $i['quantity']); @endphp
                <div class="d-flex justify-content-between fw-bold">
                    <span>Subtotal</span>
                    <span style="color:var(--tdr-gold)">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                </div>
                <div class="text-muted mt-1" style="font-size:.75rem">Ongkos kirim dihitung saat checkout</div>

                <a href="{{ route('checkout.form') }}" class="btn btn-primary w-100 fw-semibold mt-4">
                    <i class="bi bi-credit-card me-1"></i>Lanjutkan Checkout
                </a>
            </div>
        </div>

    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.cart-item-card').forEach(row => {
    const minus = row.querySelector('.qty-minus');
    const plus  = row.querySelector('.qty-plus');
    const inp   = row.querySelector('.qty-input');
    if (! inp) return;

    minus?.addEventListener('click', () => {
        if (parseInt(inp.value) > 1) { inp.value = parseInt(inp.value) - 1; inp.dispatchEvent(new Event('change')); }
    });
    plus?.addEventListener('click', () => {
        const max = parseInt(inp.max) || 99;
        if (parseInt(inp.value) < max) { inp.value = parseInt(inp.value) + 1; inp.dispatchEvent(new Event('change')); }
    });
});
</script>
@endpush
