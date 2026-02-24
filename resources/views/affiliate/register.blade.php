@extends('layouts.app')
@section('title', 'Daftar Affiliate — TDR HPZ')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            @if(session('referral_link'))
            <div class="alert alert-success">
                <h5><i class="bi bi-check-circle-fill"></i> Registrasi Berhasil!</h5>
                <p class="mb-1"><strong>Kode Referral:</strong> <code>{{ session('affiliate_code') }}</code></p>
                <p class="mb-2"><strong>Link Referral Anda:</strong></p>
                <div class="input-group">
                    <input type="text" class="form-control" id="refLink" value="{{ session('referral_link') }}" readonly>
                    <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('refLink').value);this.textContent='✓ Disalin!'">Salin</button>
                </div>
                <p class="mt-2 mb-0 small text-muted">Bagikan link ini. Setiap pembelian dari link Anda = 10% komisi otomatis.</p>
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1">Daftar Program Affiliate</h4>
                    <p class="text-muted mb-4">Dapatkan komisi 10% dari setiap penjualan melalui link Anda.</p>

                    <form method="POST" action="{{ route('affiliate.register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Budi Santoso" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="budi@email.com" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telegram Chat ID <small class="text-muted">(untuk notifikasi otomatis)</small></label>
                            <input type="text" name="telegram_id" class="form-control"
                                   value="{{ old('telegram_id') }}" placeholder="Ketik /start ke @userinfobot">
                            <div class="form-text">Opsional. Ketik <code>/start</code> ke @userinfobot di Telegram untuk mendapatkan ID Anda.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Metode Payout</label>
                            <select name="payout_method" class="form-select">
                                <option value="bank" {{ old('payout_method') === 'bank' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="ewallet" {{ old('payout_method') === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                                <option value="manual" {{ old('payout_method') === 'manual' ? 'selected' : '' }} selected>Manual</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Daftar Sekarang</button>
                    </form>

                    <hr>
                    <p class="text-center mb-0 small">
                        Sudah punya akun? <a href="{{ route('affiliate.dashboard') }}?email=">Lihat Dashboard</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
