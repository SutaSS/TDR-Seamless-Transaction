@extends('layouts.app')
@section('title', 'Checkout — TDR HPZ')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">

            @if($affiliate)
            <div class="alert alert-info py-2 mb-3">
                <i class="bi bi-tag-fill"></i>
                Anda datang dari referral <strong>{{ $affiliate->user?->name ?? $affiliate->referral_code }}</strong>.
                Komisi 10% akan otomatis tercatat.
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header fw-bold">Form Checkout</div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('checkout.process') }}" id="checkoutForm">
                        @csrf

                        <h6 class="fw-bold mb-3">Data Pembeli</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror"
                                       value="{{ old('customer_name', $user?->name) }}" required>
                                @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror"
                                       value="{{ old('customer_email', $user?->email) }}" required>
                                @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="customer_phone" class="form-control"
                                       value="{{ old('customer_phone') }}" placeholder="08xx">
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3">Pilih Produk</h6>
                        <div id="itemsContainer">
                            <div class="item-row row g-2 mb-2 align-items-end">
                                <div class="col-7">
                                    <label class="form-label">Produk</label>
                                    <select name="items[0][product_id]" class="form-select product-select" required>
                                        <option value="">— Pilih Produk —</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" data-price="{{ $p->price }}">
                                                {{ $p->name }} — Rp {{ number_format($p->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label">Qty</label>
                                    <input type="number" name="items[0][qty]" class="form-control qty-input" value="1" min="1" required>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-item w-100">✕</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addItem">
                            + Tambah Produk
                        </button>

                        <div class="d-flex justify-content-between align-items-center mb-3 border-top pt-3">
                            <span class="fw-bold">Total Estimasi:</span>
                            <span class="fs-5 fw-bold text-primary" id="totalDisplay">Rp 0</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-credit-card"></i> Bayar via Midtrans
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 1;
const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => $p->price]));

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const sel = row.querySelector('.product-select');
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const opt = sel.options[sel.selectedIndex];
        const price = parseFloat(opt?.dataset.price || 0);
        total += price * qty;
    });
    document.getElementById('totalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function buildSelect(idx) {
    return `<select name="items[${idx}][product_id]" class="form-select product-select" required>
        <option value="">— Pilih Produk —</option>
        ${products.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name} — Rp ${p.price.toLocaleString('id-ID')}</option>`).join('')}
    </select>`;
}

document.getElementById('addItem').addEventListener('click', function() {
    const container = document.getElementById('itemsContainer');
    const div = document.createElement('div');
    div.className = 'item-row row g-2 mb-2 align-items-end';
    div.innerHTML = `
        <div class="col-7"><label class="form-label">Produk</label>${buildSelect(itemIndex)}</div>
        <div class="col-3"><label class="form-label">Qty</label><input type="number" name="items[${itemIndex}][qty]" class="form-control qty-input" value="1" min="1" required></div>
        <div class="col-2 pt-4"><button type="button" class="btn btn-outline-danger btn-sm remove-item w-100">✕</button></div>`;
    container.appendChild(div);
    itemIndex++;
    attachListeners(div);
});

function attachListeners(container) {
    container.querySelectorAll('.product-select, .qty-input').forEach(el => el.addEventListener('change', recalcTotal));
    container.querySelectorAll('.remove-item').forEach(btn => btn.addEventListener('click', function() {
        if (document.querySelectorAll('.item-row').length > 1) {
            btn.closest('.item-row').remove();
            recalcTotal();
        }
    }));
}

document.querySelectorAll('.item-row').forEach(attachListeners);
</script>
@endpush
