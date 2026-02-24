@extends('layouts.app')

@section('title', 'Edit Profil - TDR HPZ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Edit Profil</h4>

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" value="{{ $user->email }}" class="form-control" disabled>
                        </div>

                        <div class="mb-4">
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
                                Diperlukan agar Anda menerima notifikasi status pesanan via Telegram.
                                <br>
                                <strong>Cara mendapatkan Chat ID:</strong>
                                Buka Telegram → cari
                                <a href="https://t.me/userinfobot" target="_blank" class="fw-semibold text-decoration-none">
                                    @userinfobot
                                </a>
                                → klik <em>Start</em> → bot akan membalas dengan <strong>ID</strong> Anda (angka).
                                <br>
                                <a href="https://t.me/userinfobot" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-telegram"></i> Buka @userinfobot di Telegram
                                </a>
                            </div>
                            @error('telegram_chat_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
