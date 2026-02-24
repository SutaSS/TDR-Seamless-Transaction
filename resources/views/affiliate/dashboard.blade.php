@extends('layouts.app')
@section('title', 'Dashboard Affiliate — TDR HPZ')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Dashboard Affiliate</h4>
            <p class="text-muted mb-0">{{ $affiliate->user?->name }} · <code>{{ $affiliate->referral_code }}</code></p>
        </div>
        <div class="input-group" style="max-width:350px">
            <input type="text" class="form-control form-control-sm" id="refLink" value="{{ $referralLink }}" readonly>
            <button class="btn btn-sm btn-outline-secondary"
                onclick="navigator.clipboard.writeText(document.getElementById('refLink').value);this.textContent='✓'">
                Salin Link
            </button>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center border-0 shadow-sm">
                <div class="text-muted small">Total Klik</div>
                <div class="fs-2 fw-bold text-primary">{{ $stats['total_clicks'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center border-0 shadow-sm">
                <div class="text-muted small">Konversi</div>
                <div class="fs-2 fw-bold text-success">{{ $stats['total_conversions'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center border-0 shadow-sm">
                <div class="text-muted small">Conversion Rate</div>
                <div class="fs-2 fw-bold text-warning">{{ $stats['conversion_rate'] }}%</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center border-0 shadow-sm">
                <div class="text-muted small">Total Komisi</div>
                <div class="fs-5 fw-bold text-danger">Rp {{ number_format($stats['total_commission'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- ── Payout Identity ────────────────────────────────────────────────── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-wallet2 text-success me-1"></i> Data Pencairan Komisi</span>
            @if($affiliate->payout_account_number)
                <span class="badge bg-success">Lengkap</span>
            @else
                <span class="badge bg-warning text-dark">Belum diisi</span>
            @endif
        </div>
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success py-2">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('affiliate.payout') }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Metode Pembayaran</label>
                        <select name="payout_method" class="form-select @error('payout_method') is-invalid @enderror" required>
                            <option value="">— Pilih —</option>
                            @foreach([
                                'ovo'          => 'OVO',
                                'gopay'        => 'GoPay',
                                'dana'         => 'DANA',
                                'shopeepay'    => 'ShopeePay',
                                'bank_bca'     => 'Bank BCA',
                                'bank_bri'     => 'Bank BRI',
                                'bank_bni'     => 'Bank BNI',
                                'bank_mandiri' => 'Bank Mandiri',
                                'manual'       => 'Manual / Lainnya',
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ old('payout_method', $affiliate->payout_method) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('payout_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Nomor Rekening / Akun</label>
                        <input type="text" name="payout_account_number"
                               value="{{ old('payout_account_number', $affiliate->payout_account_number) }}"
                               class="form-control @error('payout_account_number') is-invalid @enderror"
                               placeholder="Contoh: 081234567890" required>
                        @error('payout_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Nama Pemilik Akun</label>
                        <input type="text" name="payout_account_name"
                               value="{{ old('payout_account_name', $affiliate->payout_account_name) }}"
                               class="form-control @error('payout_account_name') is-invalid @enderror"
                               placeholder="Sesuai nama di aplikasi / buku tabungan" required>
                        @error('payout_account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success fw-semibold px-4">
                        <i class="bi bi-save me-1"></i> Simpan Data Rekening
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        {{-- Chart --}}
        <div class="col-md-7">
            <div class="card p-3">
                <h6 class="fw-bold">Klik & Konversi — 7 Hari Terakhir</h6>
                <canvas id="referralChart" height="100"></canvas>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-header fw-bold">Pesanan Terbaru</div>
                <ul class="list-group list-group-flush">
                @forelse($recentOrders as $order)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold small">#{{ $order->order_number }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ $order->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold small">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            <span class="badge bg-{{ match($order->order_status) {
                                'paid'=>'primary','shipped'=>'info text-dark','delivered'=>'success',default=>'secondary'
                            } }}" style="font-size:.7rem">{{ $order->order_status }}</span>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">Belum ada pesanan</li>
                @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('referralChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartData['labels']) !!},
        datasets: [
            {
                label: 'Klik',
                data: {!! json_encode($chartData['clicks']) !!},
                backgroundColor: 'rgba(13,110,253,.5)',
                borderColor: '#0d6efd',
                borderWidth: 1
            },
            {
                label: 'Konversi',
                data: {!! json_encode($chartData['convs']) !!},
                backgroundColor: 'rgba(25,135,84,.5)',
                borderColor: '#198754',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
</script>
@endpush
