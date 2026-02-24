@extends('layouts.app')

@section('title', 'Daftar - TDR HPZ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1 text-center">Buat Akun Baru</h4>
                    <p class="text-muted text-center small mb-4">
                        Gunakan email <code>@tdr.com</code> untuk akun admin
                    </p>

                    <form method="POST" action="{{ route('register.post') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Nama lengkap Anda" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="email@contoh.com" required>
                            <div class="form-text">
                                Email <code>@tdr.com</code> akan langsung menjadi <strong>admin</strong>.
                                Email lain akan menjadi <strong>customer</strong>.
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="tel" name="phone" value="{{ old('phone') }}"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="08123456789" required>
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Minimal 8 karakter" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control"
                                   placeholder="Ulangi password" required>
                        </div>

                        {{-- Telegram Chat ID --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Telegram Chat ID
                                <span class="text-muted fw-normal">(opsional)</span>
                            </label>
                            <input type="text" name="telegram_chat_id" value="{{ old('telegram_chat_id') }}"
                                   class="form-control @error('telegram_chat_id') is-invalid @enderror"
                                   placeholder="Contoh: 123456789">
                            <div class="form-text">
                                <i class="bi bi-telegram text-primary"></i>
                                Untuk menerima notifikasi pesanan via Telegram.
                                <br>
                                <strong>Cara mendapatkan Chat ID:</strong>
                                Buka Telegram → cari bot
                                <a href="https://t.me/userinfobot" target="_blank" class="fw-semibold text-decoration-none">
                                    @userinfobot
                                </a>
                                → klik <em>Start</em> → bot akan membalas dengan <strong>ID</strong> Anda.
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
                    <p class="text-center small mb-0">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Masuk</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
