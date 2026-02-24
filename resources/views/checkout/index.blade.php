@extends('layouts.app')
@section('title', 'Checkout — TDR HPZ')

@section('content')
<div class="container py-4">

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    @if($affiliate)
    <div class="alert alert-success py-2 mb-3">
        <i class="bi bi-tag-fill"></i>
        Referral dari <strong>{{ $affiliate->user?->name ?? $affiliate->referral_code }}</strong> aktif — komisi 10% otomatis tercatat.
    </div>
    @endif

    <form method="POST" action="{{ route('checkout.process') }}" id="checkoutForm">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div class="row g-4">

            {{-- ================================================================
                 KOLOM KIRI — Pengiriman & Data Pembeli
            ================================================================ --}}
            <div class="col-lg-7">

                {{-- 1. Alamat Pengiriman --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold border-bottom py-3">
                        <i class="bi bi-geo-alt text-primary me-1"></i> Alamat Pengiriman
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nama Penerima</label>
                                <input type="text" name="customer_name"
                                       class="form-control @error('customer_name') is-invalid @enderror"
                                       value="{{ old('customer_name', $user?->name) }}" required
                                       placeholder="Nama lengkap penerima">
                                @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nomor HP</label>
                                <input type="text" name="customer_phone"
                                       class="form-control @error('customer_phone') is-invalid @enderror"
                                       value="{{ old('customer_phone') }}" required
                                       placeholder="08xxxxxxxxxx">
                                @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Email (untuk konfirmasi)</label>
                                <input type="email" name="customer_email"
                                       class="form-control @error('customer_email') is-invalid @enderror"
                                       value="{{ old('customer_email', $user?->email) }}" required
                                       placeholder="email@contoh.com">
                                @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Alamat Lengkap</label>
                                <textarea name="shipping_address" rows="2"
                                          class="form-control @error('shipping_address') is-invalid @enderror"
                                          required placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan">{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold">Kota / Kabupaten</label>
                                <input type="text" name="shipping_city"
                                       class="form-control @error('shipping_city') is-invalid @enderror"
                                       value="{{ old('shipping_city') }}" required placeholder="Contoh: Jakarta Selatan">
                                @error('shipping_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Provinsi</label>
                                <input type="text" name="shipping_province"
                                       class="form-control @error('shipping_province') is-invalid @enderror"
                                       value="{{ old('shipping_province') }}" required placeholder="Contoh: DKI Jakarta">
                                @error('shipping_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Kode Pos</label>
                                <input type="text" name="shipping_postal_code"
                                       class="form-control @error('shipping_postal_code') is-invalid @enderror"
                                       value="{{ old('shipping_postal_code') }}" required placeholder="12345"
                                       inputmode="numeric" maxlength="10">
                                @error('shipping_postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Pilih Ekspedisi --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold border-bottom py-3">
                        <i class="bi bi-truck text-primary me-1"></i> Pilih Ekspedisi
                    </div>
                    <div class="card-body p-3">
                        @foreach($couriers as $key => $courier)
                        <label class="courier-option d-flex align-items-center gap-3 p-3 rounded mb-2 border cursor-pointer {{ old('shipping_courier') === $key ? 'border-primary bg-light' : '' }}"
                               for="courier_{{ $key }}" style="cursor:pointer">
                            <input type="radio" name="shipping_courier" id="courier_{{ $key }}"
                                   value="{{ $key }}"
                                   data-cost="{{ $courier['cost'] }}"
                                   class="form-check-input flex-shrink-0 mt-0"
                                   {{ old('shipping_courier', 'jne_reg') === $key ? 'checked' : '' }}
                                   required>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $courier['label'] }}</div>
                            </div>
                            <div class="text-end fw-bold text-primary small">
                                {{ $courier['cost'] > 0 ? 'Rp ' . number_format($courier['cost'], 0, ',', '.') : 'GRATIS' }}
                            </div>
                        </label>
                        @endforeach
                        @error('shipping_courier')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- 3. Catatan (opsional) --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold border-bottom py-3">
                        <i class="bi bi-chat-left-text text-secondary me-1"></i> Catatan <span class="fw-normal text-muted">(opsional)</span>
                    </div>
                    <div class="card-body p-4">
                        <textarea name="note" rows="2" class="form-control"
                                  placeholder="Catatan untuk penjual, misal: warna pilihan, ukuran, dll">{{ old('note') }}</textarea>
                    </div>
                </div>

            </div>{{-- end col-kiri --}}

            {{-- ================================================================
                 KOLOM KANAN — Ringkasan Pesanan
            ================================================================ --}}
            <div class="col-lg-5">
                <div class="card shadow-sm sticky-top" style="top: 16px">
                    <div class="card-header bg-white fw-bold border-bottom py-3">
                        <i class="bi bi-bag-check text-primary me-1"></i> Ringkasan Pesanan
                    </div>
                    <div class="card-body p-4">

                        {{-- Produk --}}
                        <div class="d-flex gap-3 mb-4">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" class="rounded"
                                     style="width:64px;height:64px;object-fit:cover" alt="{{ $product->name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:64px;height:64px;font-size:1.8rem">🏍</div>
                            @endif
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <div class="text-muted small">Rp {{ number_format($product->price, 0, ',', '.') }} / pcs</div>
                                @if($product->stock !== null && $product->stock <= 10 && $product->stock > 0)
                                    <div class="text-warning small">Stok tersisa {{ $product->stock }}</div>
                                @endif
                            </div>
                        </div>

                        {{-- Qty Stepper --}}
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <span class="small fw-semibold text-muted">Jumlah</span>
                            <div class="input-group" style="width:130px">
                                <button type="button" class="btn btn-outline-secondary" id="btnMinus">−</button>
                                <input type="number" name="qty" id="qtyInput"
                                       class="form-control text-center"
                                       value="{{ old('qty', $qty) }}" min="1"
                                       max="{{ $product->stock ?? 99 }}" required>
                                <button type="button" class="btn btn-outline-secondary" id="btnPlus">+</button>
                            </div>
                        </div>

                        <hr>

                        {{-- Rincian Harga --}}
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted">Subtotal</span>
                            <span class="small fw-semibold" id="subtotalDisplay">
                                Rp {{ number_format((float)$product->price * $qty, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="small text-muted">Ongkos Kirim</span>
                            <span class="small fw-semibold" id="shippingDisplay">
                                Rp {{ number_format(15000, 0, ',', '.') }}
                            </span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total Bayar</span>
                            <span class="fs-5 fw-bold text-danger" id="totalDisplay">
                                Rp {{ number_format((float)$product->price * $qty + 15000, 0, ',', '.') }}
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg fw-semibold">
                            <i class="bi bi-credit-card me-1"></i> Lanjutkan Pembayaran
                        </button>

                        <div class="text-center mt-2">
                            <small class="text-muted">
                                <i class="bi bi-shield-check text-success"></i>
                                Transaksi aman & terenkripsi via Midtrans
                            </small>
                        </div>

                    </div>
                </div>
            </div>{{-- end col-kanan --}}

        </div>{{-- end row --}}
    </form>
</div>
@endsection

@push('scripts')
<script>
const productPrice = {{ (int) round((float) $product->price) }};
const couriers = @json(collect($couriers)->map(fn($c) => $c['cost']));
let currentShippingCost = couriers['jne_reg'] ?? 15000;

function fmt(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function recalc() {
    const qty = parseInt(document.getElementById('qtyInput').value) || 1;
    const subtotal = productPrice * qty;
    const total = subtotal + currentShippingCost;
    document.getElementById('subtotalDisplay').textContent = fmt(subtotal);
    document.getElementById('shippingDisplay').textContent = fmt(currentShippingCost);
    document.getElementById('totalDisplay').textContent = fmt(total);
}

// Qty stepper
document.getElementById('btnMinus').addEventListener('click', function () {
    const inp = document.getElementById('qtyInput');
    if (parseInt(inp.value) > 1) { inp.value = parseInt(inp.value) - 1; recalc(); }
});
document.getElementById('btnPlus').addEventListener('click', function () {
    const inp = document.getElementById('qtyInput');
    const max = parseInt(inp.max) || 99;
    if (parseInt(inp.value) < max) { inp.value = parseInt(inp.value) + 1; recalc(); }
});
document.getElementById('qtyInput').addEventListener('input', recalc);

// Courier selection
document.querySelectorAll('input[name="shipping_courier"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
        currentShippingCost = parseInt(this.dataset.cost) || 0;
        recalc();
        // Highlight selected
        document.querySelectorAll('.courier-option').forEach(el => el.classList.remove('border-primary', 'bg-light'));
        this.closest('.courier-option').classList.add('border-primary', 'bg-light');
    });
});

// Init
recalc();
</script>
@endpush

