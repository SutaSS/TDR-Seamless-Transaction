@extends('layouts.app')

@section('title', 'Login - TDR HPZ')

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
    max-width: 420px;
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
                <h4 class="fw-bold mb-1">Masuk</h4>
                <p class="text-muted small mb-0">Masuk ke akun TDR HPZ Anda</p>
            </div>

            @if (session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="email@contoh.com" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Masukkan password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    <label class="form-check-label small" for="remember" style="color:var(--tdr-muted)">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                    Masuk
                </button>
            </form>

            <hr class="my-3">
            <p class="text-center small mb-0" style="color:var(--tdr-muted)">
                Belum punya akun?
                <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection
