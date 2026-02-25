@extends('layouts.app')
@section('title', 'Profil Saya — TDR HPZ')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center g-4">
        <div class="col-lg-7">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            {{-- Profile Info --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold py-3">
                    <i class="bi bi-person-circle me-1" style="color:var(--tdr-red)"></i> Informasi Profil
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                        <div class="col-12">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">
                                    Telegram Chat ID
                                    @if($user->telegram_chat_id)
                                        <span class="badge ms-1" style="background:rgba(25,135,84,0.15);color:#5dd39e"><i class="bi bi-check-circle me-1"></i>Terhubung</span>
                                    @else
                                        <span class="badge ms-1" style="background:rgba(212,168,67,0.15);color:var(--tdr-gold)">Belum terhubung</span>
                                    @endif
                                </label>
                                <input type="text" name="telegram_chat_id"
                                       value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}"
                                       class="form-control @error('telegram_chat_id') is-invalid @enderror"
                                       placeholder="Contoh: 123456789">
                                <div class="form-text">
                                    <i class="bi bi-telegram me-1" style="color:#60a5fa"></i>
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

            {{-- Change Password --}}
            <div class="card shadow-sm">
                <div class="card-header fw-bold py-3">
                    <i class="bi bi-shield-lock me-1" style="color:var(--tdr-gold)"></i> Ubah Password
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       autocomplete="current-password">
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       autocomplete="new-password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password Baru</label>
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
