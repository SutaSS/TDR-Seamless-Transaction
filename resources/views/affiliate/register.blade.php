@extends('layouts.app')
@section('title', 'Daftar Affiliate - TDR HPZ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm" style="border-top:3px solid var(--tdr-gold)">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div style="width:56px;height:56px;border-radius:14px;background:rgba(212,168,67,0.12);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
                            <i class="bi bi-currency-dollar" style="font-size:1.5rem;color:var(--tdr-gold)"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Daftar Program Affiliate</h4>
                        <p class="text-muted small">Dapatkan komisi <strong>10%</strong> dari setiap pembelian via link referral Anda.</p>
                    </div>

                    @auth
                    <div class="mb-4 p-3 rounded-3" style="background:rgba(255,255,255,0.03);border:1px solid var(--tdr-border)">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:var(--tdr-muted)">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                <div class="text-muted small">{{ auth()->user()->email }}</div>
                                @if(!auth()->user()->telegram_chat_id)
                                    <div class="small mt-1" style="color:var(--tdr-gold)">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Telegram belum terhubung —
                                        <a href="{{ route('profile.edit') }}" class="text-decoration-none">hubungkan sekarang</a>
                                        agar komisi dikirim otomatis!
                                    </div>
                                @else
                                    <div class="small mt-1" style="color:#5dd39e">
                                        <i class="bi bi-telegram me-1"></i> Telegram terhubung
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endauth

                    <form method="POST" action="{{ route('affiliate.register') }}">
                        @csrf

                        {{-- Bank / E-Wallet --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Bank / E-Wallet <span style="color:var(--tdr-red)">*</span></label>
                            <select name="bank_name" class="form-select @error('bank_name') is-invalid @enderror" required id="bankName">
                                <option value="">-- Pilih metode --</option>
                                <optgroup label="E-Wallet">
                                    <option value="OVO"       {{ old('bank_name') === 'OVO'       ? 'selected' : '' }}>OVO</option>
                                    <option value="GoPay"     {{ old('bank_name') === 'GoPay'     ? 'selected' : '' }}>GoPay</option>
                                    <option value="DANA"      {{ old('bank_name') === 'DANA'      ? 'selected' : '' }}>DANA</option>
                                    <option value="ShopeePay" {{ old('bank_name') === 'ShopeePay' ? 'selected' : '' }}>ShopeePay</option>
                                </optgroup>
                                <optgroup label="Transfer Bank">
                                    <option value="BCA"     {{ old('bank_name') === 'BCA'     ? 'selected' : '' }}>Bank BCA</option>
                                    <option value="BRI"     {{ old('bank_name') === 'BRI'     ? 'selected' : '' }}>Bank BRI</option>
                                    <option value="BNI"     {{ old('bank_name') === 'BNI'     ? 'selected' : '' }}>Bank BNI</option>
                                    <option value="Mandiri" {{ old('bank_name') === 'Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                                </optgroup>
                                <option value="Lainnya" {{ old('bank_name') === 'Lainnya' ? 'selected' : '' }}>Manual / Lainnya</option>
                            </select>
                            @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Account Number --}}
                        <div class="mb-3">
                            <label class="form-label" id="accountLabel">Nomor Rekening / Akun <span style="color:var(--tdr-red)">*</span></label>
                            <input type="text" name="bank_account_number"
                                   value="{{ old('bank_account_number') }}"
                                   class="form-control @error('bank_account_number') is-invalid @enderror"
                                   placeholder="Contoh: 081234567890 atau 1234567890" required>
                            @error('bank_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Account Holder Name --}}
                        <div class="mb-4">
                            <label class="form-label">Nama Pemilik Akun <span style="color:var(--tdr-red)">*</span></label>
                            <input type="text" name="bank_account_holder"
                                   value="{{ old('bank_account_holder', auth()->user()->name ?? '') }}"
                                   class="form-control @error('bank_account_holder') is-invalid @enderror"
                                   placeholder="Sesuai nama di buku tabungan / e-wallet" required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Pastikan nama sesuai dengan kartu identitas untuk verifikasi pencairan.
                            </div>
                            @error('bank_account_holder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="alert alert-info small py-2 mb-4">
                            <i class="bi bi-shield-check me-1"></i>
                            Pendaftaran akan <strong>ditinjau oleh admin</strong> dalam 1x24 jam. Anda akan mendapat notifikasi via Telegram setelah diapprove.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-semibold py-2">
                                <i class="bi bi-people-fill me-1"></i> Daftar Sekarang
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- How it works --}}
            <div class="card mt-4 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Cara Kerja Program Affiliate</h6>
                    <div class="d-flex gap-3 mb-3">
                        <div style="width:32px;height:32px;border-radius:50%;background:rgba(230,57,70,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;font-size:0.8rem;color:var(--tdr-red)">1</div>
                        <div><strong>Daftar & Verifikasi</strong> — Isi data rekening, tunggu approval admin (1x24 jam)</div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <div style="width:32px;height:32px;border-radius:50%;background:rgba(59,130,246,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;font-size:0.8rem;color:#60a5fa">2</div>
                        <div><strong>Bagikan Link</strong> — Sebar link unik Anda ke media sosial, grup, dll.</div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <div style="width:32px;height:32px;border-radius:50%;background:rgba(25,135,84,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;font-size:0.8rem;color:#5dd39e">3</div>
                        <div><strong>Dapat Komisi</strong> — 10% dari transaksi, langsung masuk saldo + notif Telegram real-time</div>
                    </div>
                    <div class="d-flex gap-3">
                        <div style="width:32px;height:32px;border-radius:50%;background:rgba(212,168,67,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;font-size:0.8rem;color:var(--tdr-gold)">4</div>
                        <div><strong>Cairkan Komisi</strong> — Request pencairan ke rekening yang sudah didaftarkan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('bankName').addEventListener('change', function() {
    const v = this.value;
    const label = document.getElementById('accountLabel');
    const isEwallet = ['OVO','GoPay','DANA','ShopeePay'].includes(v);
    label.innerHTML = (isEwallet ? 'Nomor HP / Akun ' : 'Nomor Rekening ')
        + '<span style="color:var(--tdr-red)">*</span>';
});
</script>
@endpush
@endsection
