@extends('layouts.app')
@section('title', 'Daftar Affiliate - TDR HPZ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="display-4 mb-2">💰</div>
                        <h4 class="fw-bold mb-1">Daftar Program Affiliate</h4>
                        <p class="text-muted small">Dapatkan komisi <strong>10%</strong> dari setiap pembelian via link referral Anda.</p>
                    </div>

                    @auth
                    <div class="alert alert-light border mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="display-6">👤</div>
                            <div>
                                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                <div class="text-muted small">{{ auth()->user()->email }}</div>
                                @if(auth()->user()->phone)
                                    <div class="text-muted small">📞 {{ auth()->user()->phone }}</div>
                                @endif
                                @if(!auth()->user()->telegram_chat_id)
                                    <div class="text-warning small mt-1">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Telegram belum terhubung —
                                        <a href="{{ route('profile.edit') }}" class="text-decoration-none">hubungkan sekarang</a>
                                        agar komisi dikirim otomatis!
                                    </div>
                                @else
                                    <div class="text-success small mt-1">
                                        <i class="bi bi-telegram"></i> Telegram terhubung ✓
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endauth

                    <form method="POST" action="{{ route('affiliate.register') }}">
                        @csrf

                        {{-- Payout Method --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metode Pembayaran Komisi <span class="text-danger">*</span></label>
                            <select name="payout_method" class="form-select @error('payout_method') is-invalid @enderror" required id="payoutMethod">
                                <option value="">— Pilih metode —</option>
                                <optgroup label="E-Wallet">
                                    <option value="ovo"       {{ old('payout_method') === 'ovo'       ? 'selected' : '' }}>OVO</option>
                                    <option value="gopay"     {{ old('payout_method') === 'gopay'     ? 'selected' : '' }}>GoPay</option>
                                    <option value="dana"      {{ old('payout_method') === 'dana'      ? 'selected' : '' }}>DANA</option>
                                    <option value="shopeepay" {{ old('payout_method') === 'shopeepay' ? 'selected' : '' }}>ShopeePay</option>
                                </optgroup>
                                <optgroup label="Transfer Bank">
                                    <option value="bank_bca"     {{ old('payout_method') === 'bank_bca'     ? 'selected' : '' }}>Bank BCA</option>
                                    <option value="bank_bri"     {{ old('payout_method') === 'bank_bri'     ? 'selected' : '' }}>Bank BRI</option>
                                    <option value="bank_bni"     {{ old('payout_method') === 'bank_bni'     ? 'selected' : '' }}>Bank BNI</option>
                                    <option value="bank_mandiri" {{ old('payout_method') === 'bank_mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                                </optgroup>
                                <option value="manual" {{ old('payout_method') === 'manual' ? 'selected' : '' }}>Manual / Lainnya</option>
                            </select>
                            @error('payout_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Account Number --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" id="accountLabel">Nomor Rekening / Akun <span class="text-danger">*</span></label>
                            <input type="text" name="payout_account_number"
                                   value="{{ old('payout_account_number') }}"
                                   class="form-control @error('payout_account_number') is-invalid @enderror"
                                   placeholder="Contoh: 081234567890 atau 1234567890" required>
                            @error('payout_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Account Holder Name --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Nama Pemilik Akun <span class="text-danger">*</span></label>
                            <input type="text" name="payout_account_name"
                                   value="{{ old('payout_account_name', auth()->user()->name ?? '') }}"
                                   class="form-control @error('payout_account_name') is-invalid @enderror"
                                   placeholder="Sesuai nama di buku tabungan / e-wallet" required>
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i>
                                Pastikan nama sesuai dengan kartu identitas untuk verifikasi pencairan.
                            </div>
                            @error('payout_account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="alert alert-info small py-2 mb-4">
                            <i class="bi bi-shield-check"></i>
                            Pendaftaran akan <strong>ditinjau oleh admin</strong> dalam 1x24 jam. Anda akan mendapat notifikasi via Telegram setelah diapprove.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-semibold py-2">
                                <i class="bi bi-people-fill"></i> Daftar Sekarang
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Cara kerja --}}
            <div class="card mt-4 shadow-sm border-0">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Cara Kerja Program Affiliate</h6>
                    <div class="d-flex gap-3 mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-weight:700">1</div>
                        <div><strong>Daftar & Verifikasi</strong> — Isi data rekening, tunggu approval admin (1x24 jam)</div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-weight:700">2</div>
                        <div><strong>Bagikan Link</strong> — Sebar link unik Anda ke media sosial, grup, dll.</div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-weight:700">3</div>
                        <div><strong>Dapat Komisi</strong> — 10% dari transaksi, langsung masuk saldo + notif Telegram real-time</div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-weight:700">4</div>
                        <div><strong>Cairkan Komisi</strong> — Request pencairan ke rekening yang sudah didaftarkan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('payoutMethod').addEventListener('change', function() {
    const v = this.value;
    const label = document.getElementById('accountLabel');
    const isEwallet = ['ovo','gopay','dana','shopeepay'].includes(v);
    label.innerHTML = (isEwallet ? 'Nomor HP / Akun ' : 'Nomor Rekening ')
        + '<span class="text-danger">*</span>';
});
</script>
@endpush
@endsection
