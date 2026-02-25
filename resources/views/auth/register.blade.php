@extends('layouts.app')

@section('title', 'Daftar - TDR HPZ')

@push('styles')
<style>
.auth-wrapper {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 0;
}
.auth-card {
    max-width: 520px;
    width: 100%;
    border-top: 3px solid var(--tdr-red) !important;
}
.auth-card .brand-mark {
    width: 48px;
    height: 48px;
    background: var(--tdr-red);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.2rem;
    margin: 0 auto 16px;
}
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="auth-card card shadow-lg">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="brand-mark"><i class="bi bi-gear-fill"></i></div>
                <h4 class="fw-bold mb-1">Buat Akun Baru</h4>
                <p class="text-muted small mb-0">
                    Gunakan email <code>@tdr-hpz.com</code> untuk akun admin
                </p>
            </div>

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Nama lengkap Anda" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="email@contoh.com" required>
                    <div class="form-text">
                        Email <code>@tdr-hpz.com</code> akan langsung menjadi <strong>admin</strong>.
                        Email lain akan menjadi <strong>customer</strong>.
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 8 karakter" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-control"
                           placeholder="Ulangi password" required>
                </div>

                {{-- Telegram Chat ID --}}
                <div class="mb-4">
                    <label class="form-label">
                        Telegram Chat ID
                        <span class="fw-normal" style="color:var(--tdr-muted)">(opsional)</span>
                    </label>
                    <input type="text" name="telegram_chat_id" value="{{ old('telegram_chat_id') }}"
                           class="form-control @error('telegram_chat_id') is-invalid @enderror"
                           placeholder="Contoh: 123456789">
                    <div class="form-text">
                        <i class="bi bi-telegram me-1" style="color:#60a5fa"></i>
                        Untuk menerima notifikasi pesanan via Telegram.
                        <br>
                        <strong>Cara mendapatkan Chat ID:</strong>
                        Buka Telegram, cari bot
                        <a href="https://t.me/userinfobot" target="_blank" class="fw-semibold text-decoration-none">
                            @userinfobot
                        </a>
                        , klik <em>Start</em>, bot akan membalas dengan <strong>ID</strong> Anda.
                    </div>
                    @error('telegram_chat_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                    Daftar Sekarang
                </button>
            </form>

            <hr class="my-3">
            <p class="text-center small mb-0" style="color:var(--tdr-muted)">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
