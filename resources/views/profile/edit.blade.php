@extends('layouts.app')
@section('title', 'Profil Saya — TDR HPZ')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center g-4">
        <div class="col-lg-7">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            {{-- ── Informasi Profil ─────────────────────────────────────── --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-person-circle text-primary me-1"></i> Informasi Profil
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nomor HP</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="08xxxxxxxxxx">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Telegram Chat ID
                                    @if($user->telegram_chat_id)
                                        <span class="badge bg-success ms-1"><i class="bi bi-check-circle"></i> Terhubung</span>
                                    @else
                                        <span class="badge bg-warning text-dark ms-1">Belum terhubung</span>
                                    @endif
                                </label>
                                <input type="text" name="telegram_chat_id"
                                       value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}"
                                       class="form-control @error('telegram_chat_id') is-invalid @enderror"
                                       placeholder="Contoh: 123456789">
                                <div class="form-text">
                                    <i class="bi bi-telegram text-primary"></i>
                                    Diperlukan untuk menerima notifikasi status pesanan.
                                    Dapatkan ID Anda di
                                    <a href="https://t.me/userinfobot" target="_blank">@userinfobot</a>.
                                </div>
                                @error('telegram_chat_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary fw-semibold px-4">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Ubah Password ────────────────────────────────────────── --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-shield-lock text-warning me-1"></i> Ubah Password
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Password Lama</label>
                                <input type="password" name="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       autocomplete="current-password">
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password Baru</label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       autocomplete="new-password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation"
                                       class="form-control" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning fw-semibold px-4">
                                <i class="bi bi-key me-1"></i> Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
