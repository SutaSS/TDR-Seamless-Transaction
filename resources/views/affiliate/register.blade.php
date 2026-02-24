@extends('layouts.app')
@section('title', 'Daftar Affiliate - TDR HPZ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1">Daftar Program Affiliate</h4>
                    <p class="text-muted small mb-4">Dapatkan komisi 10% dari setiap pembelian via link referral Anda.</p>

                    @auth
                    {{-- Info user yang sudah login --}}
                    <div class="alert alert-light border mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="display-6">👤</div>
                            <div>
                                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                <div class="text-muted small">{{ auth()->user()->email }}</div>
                                @if(!auth()->user()->telegram_chat_id)
                                    <div class="text-warning small mt-1">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Telegram belum terhubung —
                                        <a href="{{ route('profile.edit') }}" class="text-decoration-none">hubungkan sekarang</a>
                                        agar komisi dikirim otomatis!
                                    </div>
                                @else
                                    <div class="text-success small mt-1">
                                        <i class="bi bi-telegram"></i> Telegram terhubung
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endauth

                    <form method="POST" action="{{ route('affiliate.register') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Metode Pembayaran Komisi</label>
                            <select name="payout_method" class="form-select @error('payout_method') is-invalid @enderror">
                                <option value="manual">Manual (hubungi admin)</option>
                                <option value="bank" {{ old('payout_method') === 'bank' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="ewallet" {{ old('payout_method') === 'ewallet' ? 'selected' : '' }}>E-Wallet (OVO/DANA/GoPay)</option>
                            </select>
                            @error('payout_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-semibold py-2">
                                <i class="bi bi-people-fill"></i> Aktifkan Akun Affiliate
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
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:32px;height:32px;font-size:.85rem;font-weight:700">1</div>
                        <div><strong>Daftar</strong> — Aktifkan akun affiliate Anda</div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:32px;height:32px;font-size:.85rem;font-weight:700">2</div>
                        <div><strong>Bagikan</strong> — Sebar link referral unik Anda ke media sosial</div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:32px;height:32px;font-size:.85rem;font-weight:700">✓</div>
                        <div><strong>Dapat Komisi</strong> — 10% dari setiap transaksi, notifikasi via Telegram</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
