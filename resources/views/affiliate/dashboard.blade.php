@extends('layouts.app')
@section('title', 'Dashboard Affiliate — TDR HPZ')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<style>
.aff-stat-card {
    text-align: center;
    padding: 20px 12px;
    border-radius: 12px;
}
.aff-stat-card .stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1.2;
}
.aff-stat-card .stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--tdr-muted);
    margin-top: 4px;
}
</style>
@endpush

@section('content')
<div class="container">

    {{-- Pending status warning --}}
    @if($affiliate->status === 'pending')
    <div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
        <div style="font-size:1.4rem"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="fw-bold">Akun Affiliate Anda Sedang Ditinjau</div>
            <div class="small">Pendaftaran Anda telah diterima dan sedang menunggu persetujuan admin. Proses biasanya selesai dalam 1x24 jam.
            Anda akan mendapat notifikasi via Telegram setelah diapprove.</div>
        </div>
    </div>
    @elseif($affiliate->status === 'suspended')
    <div class="alert alert-danger mb-4">
        <strong>Akun Affiliate Ditangguhkan</strong> — Hubungi admin untuk informasi lebih lanjut.
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0">Dashboard Affiliate</h4>
            <p class="text-muted mb-0">{{ $affiliate->user?->name }} · <code>{{ $affiliate->referral_code }}</code>
                <span class="badge ms-1 {{ $affiliate->status === 'active' ? '' : ($affiliate->status === 'pending' ? '' : '') }}"
                      style="background:{{ $affiliate->status === 'active' ? 'rgba(25,135,84,0.15)' : ($affiliate->status === 'pending' ? 'rgba(212,168,67,0.15)' : 'rgba(230,57,70,0.15)') }};color:{{ $affiliate->status === 'active' ? '#5dd39e' : ($affiliate->status === 'pending' ? 'var(--tdr-gold)' : '#ff6b7a') }}">
                    {{ $affiliate->status }}
                </span>
            </p>
        </div>
        @if($affiliate->status === 'active')
        {{-- status active indicator only, no generic link --}}
        @endif
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card aff-stat-card">
                <div class="stat-value" style="color:#60a5fa">{{ $stats['total_clicks'] }}</div>
                <div class="stat-label">Total Klik</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card aff-stat-card">
                <div class="stat-value" style="color:#5dd39e">{{ $stats['total_conversions'] }}</div>
                <div class="stat-label">Konversi</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card aff-stat-card">
                <div class="stat-value" style="color:var(--tdr-gold)">{{ $stats['conversion_rate'] }}%</div>
                <div class="stat-label">Conversion Rate</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card aff-stat-card">
                <div class="stat-value" style="color:var(--tdr-red);font-size:1.25rem">Rp {{ number_format($stats['total_commission'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Komisi</div>
            </div>
        </div>
    </div>

    {{-- Per-product affiliate links --}}
    @if($affiliate->status === 'active' && $products->isNotEmpty())
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold py-3 d-flex align-items-center gap-2">
            <i class="bi bi-link-45deg" style="color:var(--tdr-gold)"></i>
            Link Affiliate per Produk
            <span class="text-muted fw-normal" style="font-size:.8rem">— bagikan link ini agar klik terlacak</span>
        </div>
        <div class="card-body p-3">
            <div class="d-flex flex-column gap-2">
                @foreach($products as $prod)
                @php $affUrl = url('/products/' . $prod->slug) . '?ref=' . $affiliate->referral_code; @endphp
                <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:rgba(255,255,255,.03);border:1px solid var(--tdr-border)">
                    <div class="flex-grow-1 min-width-0" style="overflow:hidden">
                        <div class="fw-semibold" style="font-size:.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $prod->name }}</div>
                        <div class="text-muted" style="font-size:.72rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $affUrl }}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0"
                            style="font-size:.75rem;white-space:nowrap"
                            onclick="copyAffLink('{{ $affUrl }}', this)">
                        <i class="bi bi-clipboard me-1"></i>Salin
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Saldo & Pencairan --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            @if(session('withdraw_success'))
                <div class="alert alert-success py-2 mb-3">
                    <i class="bi bi-check-circle me-1"></i> {{ session('withdraw_success') }}
                </div>
            @endif
            @if(session('withdraw_error'))
                <div class="alert alert-danger py-2 mb-3">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('withdraw_error') }}
                </div>
            @endif

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="text-muted small mb-1"><i class="bi bi-wallet2 me-1"></i>Saldo Komisi Tersedia</div>
                    <div class="fw-bold" style="font-size:1.75rem;color:var(--tdr-gold)">
                        Rp {{ number_format($stats['balance'], 0, ',', '.') }}
                    </div>
                    <div class="text-muted" style="font-size:.75rem">Minimum pencairan: Rp 50.000</div>
                </div>

                <div>
                    @if($affiliate->status !== 'active')
                        <span class="badge px-3 py-2" style="background:rgba(212,168,67,.15);color:var(--tdr-gold);font-size:.8rem">
                            <i class="bi bi-lock me-1"></i> Akun belum aktif
                        </span>
                    @elseif(! $affiliate->bank_account_number)
                        <span class="badge px-3 py-2" style="background:rgba(230,57,70,.15);color:var(--tdr-red);font-size:.8rem">
                            <i class="bi bi-exclamation-circle me-1"></i> Isi data rekening dulu
                        </span>
                    @elseif($pendingWithdrawal)
                        <div class="text-end">
                            <span class="badge px-3 py-2" style="background:rgba(212,168,67,.15);color:var(--tdr-gold);font-size:.8rem">
                                <i class="bi bi-hourglass-split me-1"></i> Pencairan sedang diproses
                            </span>
                            <div class="text-muted mt-1" style="font-size:.72rem">
                                Rp {{ number_format($pendingWithdrawal->amount, 0, ',', '.') }}
                                · {{ $pendingWithdrawal->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                            </div>
                        </div>
                    @elseif($stats['balance'] < 50000)
                        <span class="badge px-3 py-2" style="background:rgba(255,255,255,.05);color:var(--tdr-muted);font-size:.8rem">
                            <i class="bi bi-info-circle me-1"></i> Saldo belum mencukupi
                        </span>
                    @else
                        @php $balanceFmt = number_format($stats['balance'], 0, ',', '.'); @endphp
                        <form method="POST" action="{{ route('affiliate.withdraw') }}"
                              onsubmit="return confirm('Cairkan semua saldo Rp {{ $balanceFmt }} ke {{ $affiliate->bank_name }} ({{ $affiliate->bank_account_number }})?')">
                            @csrf
                            <button type="submit" class="btn fw-semibold px-4"
                                    style="background:var(--tdr-gold);color:#0b0b0f;border-radius:8px">
                                <i class="bi bi-cash-coin me-1"></i> Cairkan Rp {{ $balanceFmt }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Payout Identity --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold py-3 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-wallet2 me-1" style="color:#5dd39e"></i> Data Pencairan Komisi</span>
            @if($affiliate->bank_account_number)
                <span class="badge" style="background:rgba(25,135,84,0.15);color:#5dd39e">Lengkap</span>
            @else
                <span class="badge" style="background:rgba(212,168,67,0.15);color:var(--tdr-gold)">Belum diisi</span>
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
                        <label class="form-label small">Nama Bank / E-Wallet</label>
                        <select name="bank_name" class="form-select @error('bank_name') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            @foreach([
                                'OVO'          => 'OVO',
                                'GoPay'        => 'GoPay',
                                'DANA'         => 'DANA',
                                'ShopeePay'    => 'ShopeePay',
                                'BCA'          => 'Bank BCA',
                                'BRI'          => 'Bank BRI',
                                'BNI'          => 'Bank BNI',
                                'Mandiri'      => 'Bank Mandiri',
                                'Lainnya'      => 'Manual / Lainnya',
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ old('bank_name', $affiliate->bank_name) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Nomor Rekening / Akun</label>
                        <input type="text" name="bank_account_number"
                               value="{{ old('bank_account_number', $affiliate->bank_account_number) }}"
                               class="form-control @error('bank_account_number') is-invalid @enderror"
                               placeholder="Contoh: 081234567890" required>
                        @error('bank_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Nama Pemilik Akun</label>
                        <input type="text" name="bank_account_holder"
                               value="{{ old('bank_account_holder', $affiliate->bank_account_holder) }}"
                               class="form-control @error('bank_account_holder') is-invalid @enderror"
                               placeholder="Sesuai nama di aplikasi / buku tabungan" required>
                        @error('bank_account_holder')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                <h6 class="fw-bold mb-3">Klik & Konversi — 7 Hari Terakhir</h6>
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
                            <span class="badge bg-{{ match($order->status) {
                                'verified'=>'primary','shipped'=>'info text-dark','completed'=>'success',default=>'secondary'
                            } }}" style="font-size:.7rem">{{ $order->status }}</span>
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
function copyAffLink(url, btn) {
    navigator.clipboard.writeText(url).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Disalin!';
        setTimeout(() => btn.innerHTML = orig, 2000);
    });
}
new Chart(document.getElementById('referralChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartData['labels']) !!},
        datasets: [
            {
                label: 'Klik',
                data: {!! json_encode($chartData['clicks']) !!},
                backgroundColor: 'rgba(96,165,250,.4)',
                borderColor: '#60a5fa',
                borderWidth: 1,
                borderRadius: 4
            },
            {
                label: 'Konversi',
                data: {!! json_encode($chartData['convs']) !!},
                backgroundColor: 'rgba(93,211,158,.4)',
                borderColor: '#5dd39e',
                borderWidth: 1,
                borderRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { color: '#8b8fa3', font: { family: 'Inter' } } }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0, color: '#6b7084' },
                grid: { color: 'rgba(255,255,255,0.04)' }
            },
            x: {
                ticks: { color: '#6b7084' },
                grid: { display: false }
            }
        }
    }
});
</script>
@endpush
