@extends('layouts.app')
@section('title', 'Dashboard Affiliate — TDR HPZ')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="container">

    {{-- Pending status warning --}}
    @if($affiliate->status === 'pending')
    <div class="alert alert-warning border-warning d-flex align-items-start gap-3 mb-4">
        <div style="font-size:1.8rem">⏳</div>
        <div>
            <div class="fw-bold">Akun Affiliate Anda Sedang Ditinjau</div>
            <div class="small">Pendaftaran Anda telah diterima dan sedang menunggu persetujuan admin. Proses biasanya selesai dalam 1x24 jam.
            Anda akan mendapat notifikasi via Telegram setelah diapprove.</div>
        </div>
    </div>
    @elseif($affiliate->status === 'rejected')
    <div class="alert alert-danger mb-4">
        <strong>Akun Affiliate Ditolak</strong> — Hubungi admin untuk informasi lebih lanjut.
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Dashboard Affiliate</h4>
            <p class="text-muted mb-0">{{ $affiliate->user?->name }} · <code>{{ $affiliate->referral_code }}</code>
                <span class="badge {{ $affiliate->status === 'approved' ? 'bg-success' : ($affiliate->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }} ms-1">{{ $affiliate->status }}</span>
            </p>
        </div>
        @if($affiliate->status === 'approved')
        <div class="input-group" style="max-width:350px">
            <input type="text" class="form-control form-control-sm" id="refLink" value="{{ $referralLink }}" readonly>
            <button class="btn btn-sm btn-outline-secondary"
                onclick="navigator.clipboard.writeText(document.getElementById('refLink').value);this.textContent='✓ Disalin'">
                Salin Link
            </button>
        </div>
        @endif
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
                <div class="text-muted" style="font-size:.7rem">semua status</div>
            </div>
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════════════ --}}
    {{-- Commission Breakdown + Payout Button --}}
    {{-- ═════════════════════════════════════════════════════════════════════ --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-cash-coin text-warning me-1"></i> Status Komisi</span>
            @if($commissionApproved > 0)
                <span class="badge bg-success fs-6">
                    Rp {{ number_format($commissionApproved, 0, ',', '.') }} siap dicairkan
                </span>
            @endif
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <div class="rounded-3 p-3 text-center" style="background:#fff8e1">
                        <div class="text-muted small mb-1">⏳ Menunggu Konfirmasi</div>
                        <div class="fw-bold fs-5">Rp {{ number_format($commissionPending, 0, ',', '.') }}</div>
                        <div class="text-muted" style="font-size:.7rem">Pesanan belum delivered</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="rounded-3 p-3 text-center" style="background:#e8f5e9">
                        <div class="text-muted small mb-1">✅ Siap Dicairkan</div>
                        <div class="fw-bold fs-5 text-success">Rp {{ number_format($commissionApproved, 0, ',', '.') }}</div>
                        <div class="text-muted" style="font-size:.7rem">Pesanan sudah delivered</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="rounded-3 p-3 text-center" style="background:#e3f2fd">
                        <div class="text-muted small mb-1">💸 Sudah Dicairkan</div>
                        <div class="fw-bold fs-5 text-primary">Rp {{ number_format($commissionPaid, 0, ',', '.') }}</div>
                        <div class="text-muted" style="font-size:.7rem">Total payout</div>
                    </div>
                </div>
            </div>

            {{-- Payout button --}}
            @if($commissionApproved > 0 && $affiliate->payout_account_number)
                @php
                    $confirmMsg = 'Ajukan pencairan Rp ' . number_format($commissionApproved, 0, ',', '.') . ' ke ' . ($affiliate->payout_method ? strtoupper($affiliate->payout_method) : '-') . ' ' . $affiliate->payout_account_number . '?';
                @endphp
                <form method="POST" action="{{ route('affiliate.payout.request') }}"
                      onsubmit="return confirm({{ json_encode($confirmMsg) }})">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg px-5 fw-semibold">
                        <i class="bi bi-send-check me-2"></i>
                        Cairkan Komisi &mdash; Rp {{ number_format($commissionApproved, 0, ',', '.') }}
                    </button>
                </form>
            @elseif($commissionApproved > 0 && !$affiliate->payout_account_number)
                <div class="alert alert-warning py-2 mb-0">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Ada <strong>Rp {{ number_format($commissionApproved, 0, ',', '.') }}</strong> siap dicairkan,
                    tapi data rekening belum lengkap! Isi form rekening di bawah dulu.
                </div>
            @else
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Belum ada komisi yang siap dicairkan. Komisi akan terkonfirmasi otomatis setelah pesanan diterima pembeli.
                </div>
            @endif
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
                            <span class="badge badge-{{ $order->order_status }}" style="font-size:.7rem">{{ $order->order_status }}</span>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">Belum ada pesanan</li>
                @endforelse
                </ul>
            </div>
        </div>
    </div>


    {{-- ══ Conversions Table ══ --}}
    <div class="card shadow-sm mt-4 mb-5">
        <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-table text-primary me-1"></i> Riwayat Komisi</span>
            <span class="badge bg-secondary">{{ $conversions->count() }} konversi</span>
        </div>
        @if($conversions->isEmpty())
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-inbox display-5"></i>
                <p class="mt-2">Belum ada konversi. Mulai bagikan link referral Anda!</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Pesanan</th>
                        <th>Tanggal</th>
                        <th class="text-end">Nilai Order</th>
                        <th class="text-end">Komisi</th>
                        <th class="text-center">Status Komisi</th>
                        <th class="text-center">Status Order</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($conversions as $conv)
                    @php
                        $commStatusBadge = match($conv->status) {
                            'pending'  => ['bg-warning text-dark', '⏳ Menunggu'],
                            'approved' => ['bg-success',           '✅ Siap Cairkan'],
                            'paid'     => ['bg-primary',           '💸 Dicairkan'],
                            'rejected' => ['bg-danger',            '❌ Ditolak'],
                            default    => ['bg-secondary',         $conv->status],
                        };
                    @endphp
                    <tr>
                        <td class="fw-semibold font-monospace">
                            <a href="{{ route('orders.show', $conv->order_id) }}" class="text-decoration-none">
                                #{{ $conv->order?->order_number ?? $conv->order_id }}
                            </a>
                        </td>
                        <td class="text-muted">{{ $conv->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">Rp {{ number_format($conv->order?->total_amount ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end fw-semibold text-success">Rp {{ number_format($conv->commission_amount, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $commStatusBadge[0] }}">{{ $commStatusBadge[1] }}</span>
                            @if($conv->paid_at)
                                <div class="text-muted" style="font-size:.68rem">{{ $conv->paid_at->format('d/m/Y') }}</div>
                            @elseif($conv->approved_at)
                                <div class="text-muted" style="font-size:.68rem">{{ $conv->approved_at->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-{{ $conv->order?->order_status ?? 'secondary' }}">
                                {{ $conv->order?->order_status ?? '-' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
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
