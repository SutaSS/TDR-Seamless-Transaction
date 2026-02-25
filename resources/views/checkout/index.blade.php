@extends('layouts.app')
@section('title', 'Checkout — TDR HPZ')

@section('content')
<div class="container py-4">

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if(session('payment_pending'))
    @php $pending = session('payment_pending'); @endphp
    <div class="alert py-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2"
         style="background:rgba(212,168,67,.1);border:1px solid rgba(212,168,67,.35);border-radius:10px;color:var(--tdr-gold)">
        <div>
            <i class="bi bi-clock-history me-1"></i>
            <strong>Pembayaran belum selesai</strong> &mdash; Order <code>{{ $pending['order_number'] }}</code> masih menunggu pembayaran.
        </div>
        <a href="{{ $pending['snap_url'] }}" class="btn btn-sm fw-semibold"
           style="background:var(--tdr-gold);color:#0b0b0f;border-radius:6px;white-space:nowrap">
            <i class="bi bi-credit-card me-1"></i>Lanjutkan Pembayaran
        </a>
    </div>
    @endif

    @if($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    @php
        $hasAffiliate = collect($affiliates)->filter()->isNotEmpty();
    @endphp
    @if($hasAffiliate)
    <div class="alert alert-success py-2 mb-3">
        <i class="bi bi-tag-fill me-1"></i>
        Kode referral aktif pada beberapa item — komisi affiliate otomatis tercatat.
    </div>
    @endif

    <form method="POST" action="{{ route('checkout.process') }}" id="checkoutForm">
        @csrf

        <div class="row g-4">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-7">

                {{-- Shipping Address --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header fw-bold py-3">
                        <i class="bi bi-geo-alt me-1" style="color:var(--tdr-red)"></i> Alamat Pengiriman
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

                {{-- Courier Selection --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header fw-bold py-3">
                        <i class="bi bi-truck me-1" style="color:var(--tdr-red)"></i> Pilih Ekspedisi
                    </div>
                    <div class="card-body p-3">
                        @foreach($couriers as $key => $courier)
                        <label class="courier-option d-flex align-items-center gap-3 p-3 rounded-3 mb-2 {{ old('shipping_courier') === $key ? 'selected' : '' }}"
                               for="courier_{{ $key }}" style="cursor:pointer;border:1px solid var(--tdr-border);transition:all .15s ease">
                            <input type="radio" name="shipping_courier" id="courier_{{ $key }}"
                                   value="{{ $key }}"
                                   data-cost="{{ $courier['cost'] }}"
                                   class="form-check-input flex-shrink-0 mt-0"
                                   {{ old('shipping_courier', 'jne_reg') === $key ? 'checked' : '' }}
                                   required>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $courier['label'] }}</div>
                            </div>
                            <div class="text-end fw-bold small" style="color:var(--tdr-red)">
                                {{ $courier['cost'] > 0 ? 'Rp ' . number_format($courier['cost'], 0, ',', '.') : 'GRATIS' }}
                            </div>
                        </label>
                        @endforeach
                        @error('shipping_courier')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Notes --}}
                <div class="card shadow-sm">
                    <div class="card-header fw-bold py-3">
                        <i class="bi bi-chat-left-text me-1" style="color:var(--tdr-muted)"></i> Catatan <span class="fw-normal text-muted">(opsional)</span>
                    </div>
                    <div class="card-body p-4">
                        <textarea name="notes" rows="2" class="form-control"
                                  placeholder="Catatan untuk penjual, misal: warna pilihan, ukuran, dll">{{ old('note') }}</textarea>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-5">
                <div class="card shadow-sm sticky-top" style="top: 16px">
                    <div class="card-header fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bag-check me-1" style="color:var(--tdr-red)"></i> Ringkasan Pesanan</span>
                        <a href="{{ route('cart.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    </div>
                    <div class="card-body p-4">

                        {{-- Cart items --}}
                        @php $cartSubtotal = 0; @endphp
                        @foreach($cart as $productId => $item)
                        @php
                            $lineTotal = $item['product_price'] * $item['quantity'];
                            $cartSubtotal += $lineTotal;
                            $itemAffiliate = $affiliates[$productId] ?? null;
                        @endphp
                        <div class="d-flex gap-3 mb-3 pb-3" style="border-bottom:1px solid var(--tdr-border)">
                            {{-- Thumbnail --}}
                            <div class="flex-shrink-0 rounded d-flex align-items-center justify-content-center overflow-hidden"
                                 style="width:48px;height:48px;background:rgba(255,255,255,0.04)">
                                @if($item['thumbnail_url'] ?? null)
                                    <img src="{{ $item['thumbnail_url'] }}" style="width:100%;height:100%;object-fit:cover" alt="">
                                @else
                                    <i class="bi bi-box-seam" style="color:var(--tdr-muted)"></i>
                                @endif
                            </div>
                            {{-- Info --}}
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size:.82rem">{{ $item['product_name'] }}</div>
                                <div class="text-muted" style="font-size:.75rem">
                                    {{ $item['quantity'] }} × Rp {{ number_format($item['product_price'], 0, ',', '.') }}
                                </div>
                                @if($itemAffiliate)
                                <div class="mt-1">
                                    <span style="display:inline-flex;align-items:center;gap:3px;padding:1px 6px;border-radius:20px;font-size:.68rem;font-weight:600;background:rgba(212,168,67,.12);color:var(--tdr-gold);border:1px solid rgba(212,168,67,.2)">
                                        <i class="bi bi-tag"></i>
                                        Ref: {{ $item['affiliate_code'] }}
                                        ({{ $itemAffiliate->user?->name ?? '' }})
                                    </span>
                                </div>
                                @elseif(! empty($item['affiliate_code']))
                                <div class="mt-1">
                                    <span style="display:inline-flex;align-items:center;gap:3px;padding:1px 6px;border-radius:20px;font-size:.68rem;font-weight:600;background:rgba(212,168,67,.12);color:var(--tdr-gold);border:1px solid rgba(212,168,67,.2)">
                                        <i class="bi bi-tag"></i>Ref: {{ $item['affiliate_code'] }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            {{-- Line total --}}
                            <div class="text-end fw-semibold flex-shrink-0" style="font-size:.82rem;color:var(--tdr-gold)">
                                Rp {{ number_format($lineTotal, 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach

                        {{-- Subtotal --}}
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted">Subtotal ({{ count($cart) }} item)</span>
                            <span class="small fw-semibold" id="subtotalDisplay">
                                Rp {{ number_format($cartSubtotal, 0, ',', '.') }}
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
                            <span class="fs-5 fw-bold" style="color:var(--tdr-red)" id="totalDisplay">
                                Rp {{ number_format($cartSubtotal + 15000, 0, ',', '.') }}
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg fw-semibold">
                            <i class="bi bi-credit-card me-1"></i> Lanjutkan Pembayaran
                        </button>

                        <div class="text-center mt-2">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1" style="color:#5dd39e"></i>
                                Transaksi aman & terenkripsi via Midtrans
                            </small>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.courier-option.selected,
.courier-option:has(input:checked) {
    border-color: var(--tdr-red) !important;
    background: rgba(230,57,70,0.05);
}
</style>
@endpush

@push('scripts')
<script>
const cartSubtotal = {{ (int) round(collect($cart)->sum(fn($i) => $i['product_price'] * $i['quantity'])) }};
const couriers = @json(collect($couriers)->map(fn($c) => $c['cost']));
let currentShippingCost = couriers['jne_reg'] ?? 15000;

function fmt(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function recalc() {
    const total = cartSubtotal + currentShippingCost;
    document.getElementById('shippingDisplay').textContent = fmt(currentShippingCost);
    document.getElementById('totalDisplay').textContent = fmt(total);
}

// Courier selection
document.querySelectorAll('input[name="shipping_courier"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
        currentShippingCost = parseInt(this.dataset.cost) || 0;
        recalc();
        document.querySelectorAll('.courier-option').forEach(el => el.classList.remove('selected'));
        this.closest('.courier-option').classList.add('selected');
    });
});

// Init
recalc();
</script>
@endpush
