@extends('layouts.app')
@section('title', 'Aktifkan Notifikasi Telegram — TDR HPZ')

@push('styles')
<style>
    .setup-card {
        background: var(--tdr-card-bg);
        border: 1px solid var(--tdr-border);
        border-radius: 20px;
        padding: 40px 36px;
        max-width: 480px;
        margin: 0 auto;
    }
    .step-row {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 14px 0;
        border-bottom: 1px solid var(--tdr-border);
    }
    .step-row:last-child { border-bottom: none; }
    .step-num {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(212,168,67,.15);
        border: 1px solid rgba(212,168,67,.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        font-weight: 700;
        color: var(--tdr-gold);
        margin-top: 2px;
    }
    .btn-telegram {
        background: #0088cc;
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-size: 1rem;
        font-weight: 600;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        transition: background .2s;
    }
    .btn-telegram:hover { background: #0099dd; color: #fff; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="setup-card animate-in">

        {{-- Icon + heading --}}
        <div class="text-center mb-4">
            <div style="width:72px;height:72px;border-radius:50%;background:rgba(0,136,204,.12);border:1px solid rgba(0,136,204,.25);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px">
                <i class="bi bi-telegram" style="font-size:2rem;color:#5eb8d4"></i>
            </div>
            <h4 class="fw-bold mb-1">Aktifkan Notifikasi Telegram</h4>
            <p style="color:var(--tdr-muted);font-size:.9rem;margin:0">
                Dapatkan update langsung di Telegram setiap kali status pesananmu berubah —
                konfirmasi bayar, pengiriman, hingga selesai.
            </p>
        </div>

        {{-- Steps --}}
        <div class="mb-4" style="background:rgba(255,255,255,.03);border:1px solid var(--tdr-border);border-radius:12px;padding:6px 16px">
            <div class="step-row">
                <span class="step-num">1</span>
                <div>
                    <div class="fw-semibold" style="font-size:.9rem">Buka bot Telegram di bawah</div>
                    <div style="color:var(--tdr-muted);font-size:.8rem">Klik tombol biru dan kamu akan diarahkan ke aplikasi Telegram.</div>
                </div>
            </div>
            <div class="step-row">
                <span class="step-num">2</span>
                <div>
                    <div class="fw-semibold" style="font-size:.9rem">Ketik <code>/start</code> ke bot</div>
                    <div style="color:var(--tdr-muted);font-size:.8rem">Bot akan membalas dengan <strong>Chat ID</strong> unik milikmu.</div>
                </div>
            </div>
            <div class="step-row">
                <span class="step-num">3</span>
                <div>
                    <div class="fw-semibold" style="font-size:.9rem">Salin Chat ID → simpan di Profil</div>
                    <div style="color:var(--tdr-muted);font-size:.8rem">
                        Tempel angka tersebut di
                        <a href="{{ route('profile.edit') }}" style="color:var(--tdr-gold)">halaman Profil</a>
                        pada kolom "Telegram Chat ID".
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA: open bot --}}
        <a href="https://t.me/{{ $botUsername }}" target="_blank" class="btn-telegram mb-3">
            <i class="bi bi-telegram" style="font-size:1.25rem"></i>
            Buka @{{ $botUsername }}
        </a>

        {{-- Secondary actions --}}
        <div class="d-flex gap-2">
            <a href="{{ route('profile.edit') }}"
               class="btn btn-outline-light flex-fill"
               style="border-radius:10px;font-size:.875rem">
                <i class="bi bi-person-gear me-1"></i>Atur di Profil
            </a>
            <a href="{{ route('home') }}"
               class="btn flex-fill"
               style="background:rgba(255,255,255,.05);color:var(--tdr-muted);border:1px solid var(--tdr-border);border-radius:10px;font-size:.875rem">
                Lewati
            </a>
        </div>

        @if(auth()->user()->telegram_chat_id)
        <div class="mt-3 p-3 rounded-3 text-center" style="background:rgba(25,135,84,.1);border:1px solid rgba(25,135,84,.2);font-size:.8rem;color:#5dd39e">
            <i class="bi bi-check-circle me-1"></i> Telegram sudah terhubung (Chat ID: <code>{{ auth()->user()->telegram_chat_id }}</code>)
        </div>
        @endif

    </div>
</div>
@endsection
