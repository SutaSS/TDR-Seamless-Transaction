@extends('layouts.app')
@section('title', 'Beranda - TDR HPZ Store')

@push('styles')
<style>
.hero-landing { background: linear-gradient(135deg, #1d3557 0%, #457b9d 100%); color: #fff; padding: 80px 0 60px; }
.feature-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 12px; }
.testimonial-card { border-left: 4px solid #0d6efd; }
.product-card-sm { transition: transform .15s ease; }
.product-card-sm:hover { transform: translateY(-3px); }
.stat-hero { text-align: center; }
.stat-hero .num { font-size: 2.5rem; font-weight: 800; color: #ffd700; }
.stat-hero .lbl { font-size: .85rem; opacity: .85; }
</style>
@endpush

@section('content')

{{-- ── HERO ─────────────────────────────────────────────────────────────── --}}
<div class="hero-landing">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-lg-start">
                <div class="badge bg-warning text-dark mb-3 px-3 py-2 fw-semibold">🔥 Terpercaya sejak 2020</div>
                <h1 class="fw-bold display-4 mb-3">Spare Part Motor <span style="color:#ffd700">Berkualitas</span></h1>
                <p class="lead mb-4 opacity-75">
                    TDR HPZ — distributor resmi spare part motor terbaik. Harga kompetitif, stok lengkap, pengiriman ke seluruh Indonesia.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="{{ route('shop') }}" class="btn btn-warning btn-lg fw-bold px-5">
                        <i class="bi bi-shop"></i> Lihat Produk
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">
                        Daftar Gratis
                    </a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="row g-3">
                    <div class="col-4 stat-hero">
                        <div class="num">500+</div>
                        <div class="lbl">Produk tersedia</div>
                    </div>
                    <div class="col-4 stat-hero">
                        <div class="num">10K+</div>
                        <div class="lbl">Pelanggan puas</div>
                    </div>
                    <div class="col-4 stat-hero">
                        <div class="num">4.9★</div>
                        <div class="lbl">Rating toko</div>
                    </div>
                </div>
                <div class="mt-4 p-4 rounded-3" style="background:rgba(255,255,255,.1);backdrop-filter:blur(10px)">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:44px;height:44px;background:#198754;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem">✅</div>
                        <div>
                            <div class="fw-semibold">Pembayaran Aman</div>
                            <small class="opacity-75">Dijamin via Midtrans / Snap</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:44px;height:44px;background:#0d6efd;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem">📦</div>
                        <div>
                            <div class="fw-semibold">Pengiriman Cepat</div>
                            <small class="opacity-75">JNE / J&T / SiCepat — terlacak real-time</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── FEATURED PRODUCTS ───────────────────────────────────────────────── --}}
@if($featuredProducts->isNotEmpty())
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Produk Unggulan</h3>
            <p class="text-muted small mb-0">Pilihan terlaris dari katalog kami</p>
        </div>
        <a href="{{ route('shop') }}" class="btn btn-outline-primary">
            Semua Produk <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="row g-4">
        @foreach($featuredProducts as $product)
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 border-0 shadow-sm product-card-sm">
                <div class="bg-light d-flex align-items-center justify-content-center" style="height:120px;font-size:2rem">🏍</div>
                <div class="card-body p-2">
                    <div class="fw-semibold small" style="line-height:1.3;font-size:.8rem">{{ \Illuminate\Support\Str::limit($product->name, 35) }}</div>
                    <div class="text-danger fw-bold small mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                </div>
                <div class="card-footer bg-white border-0 p-2 pt-0">
                    <a href="{{ route('shop') }}" class="btn btn-primary btn-sm w-100" style="font-size:.75rem">Beli</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<hr class="my-0">
@endif

{{-- ── WHY US / FEATURES ───────────────────────────────────────────────── --}}
<div class="container py-5">
    <div class="text-center mb-5">
        <h3 class="fw-bold">Kenapa Pilih TDR HPZ?</h3>
        <p class="text-muted">Kami berkomitmen memberikan pengalaman belanja terbaik untuk kebutuhan motor Anda</p>
    </div>
    <div class="row g-4 text-center">
        <div class="col-6 col-md-3">
            <div class="feature-icon bg-primary bg-opacity-10">🏆</div>
            <h6 class="fw-bold">Produk Original</h6>
            <p class="text-muted small">100% produk asli bersertifikat, kami tidak jual produk KW</p>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-icon bg-success bg-opacity-10">🚚</div>
            <h6 class="fw-bold">Pengiriman Cepat</h6>
            <p class="text-muted small">Same-day processing, estimasi 1–3 hari ke seluruh Indonesia</p>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-icon bg-warning bg-opacity-10">💬</div>
            <h6 class="fw-bold">Notifikasi Real-Time</h6>
            <p class="text-muted small">Update status pesanan langsung ke Telegram Anda, 24/7</p>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-icon bg-danger bg-opacity-10">🔒</div>
            <h6 class="fw-bold">Pembayaran Aman</h6>
            <p class="text-muted small">Diproses via Midtrans — bank transfer, QRIS & e-wallet</p>
        </div>
    </div>
</div>

{{-- ── TESTIMONIALS ────────────────────────────────────────────────────── --}}
<div style="background:#f8f9fa" class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Apa Kata Pelanggan Kami</h4>
        </div>
        <div class="row g-4">
            @foreach([
                ['Budi Santoso',    'Mekanik Bengkel', '⭐⭐⭐⭐⭐', 'Sudah 3 tahun langganan di sini. Kualitas spare part selalu konsisten dan harga jauh lebih murah dari toko offline.'],
                ['Siti Rahayu',     'Pengguna Honda Beat', '⭐⭐⭐⭐⭐', 'Pemesanan gampang, langsung beli dan bayar. Notif Telegram-nya keren, bisa tahu status pesanan real-time!'],
                ['Dimas Prasetyo',  'Mekanik Freelance', '⭐⭐⭐⭐⭐', 'Program affiliate TDR-HPZ bantu penghasilan tambahan. Komisi 10% cair otomatis tanpa ribet follow-up admin.'],
            ] as [$name, $role, $stars, $text])
            <div class="col-md-4">
                <div class="card border-0 p-4 shadow-sm testimonial-card h-100">
                    <div class="mb-2">{{ $stars }}</div>
                    <p class="text-muted small mb-3 flex-grow-1">{{ $text }}</p>
                    <div class="fw-semibold">{{ $name }}</div>
                    <small class="text-muted">{{ $role }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── AFFILIATE CTA ───────────────────────────────────────────────────── --}}
<div class="py-5" style="background: linear-gradient(135deg, #1d3557 0%, #2d6a4f 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7 text-white mb-4 mb-md-0">
                <div class="badge bg-warning text-dark mb-2">💰 Program Affiliate</div>
                <h3 class="fw-bold">Hasilkan Uang dari Referral Anda</h3>
                <ul class="list-unstyled mt-3 mb-0" style="opacity:.9">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> Komisi 10% dari setiap pembelian via link Anda</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i> Notifikasi otomatis via Telegram saat komisi masuk</li>
                    <li><i class="bi bi-check-circle-fill text-warning me-2"></i> Pencairan ke rekening bank / e-wallet (OVO, DANA, GoPay)</li>
                </ul>
            </div>
            <div class="col-md-5 text-center">
                @auth
                    @if(auth()->user()->role === 'customer')
                        <a href="{{ route('affiliate.register.form') }}" class="btn btn-warning btn-lg fw-bold px-5">
                            <i class="bi bi-people-fill me-2"></i>Daftar Affiliate
                        </a>
                    @elseif(auth()->user()->role === 'affiliate')
                        <a href="{{ route('affiliate.dashboard') }}" class="btn btn-warning btn-lg fw-bold px-5">
                            <i class="bi bi-graph-up me-2"></i>Dashboard Saya
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold px-5 mb-2">
                        <i class="bi bi-people-fill me-2"></i>Daftar & Jadi Affiliate
                    </a>
                    <p class="text-white-50 small mt-2 mb-0">Gratis! Persetujuan admin 1×24 jam</p>
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- ── HOW TO ORDER ─────────────────────────────────────────────────────── --}}
<div class="container py-5">
    <div class="text-center mb-4">
        <h4 class="fw-bold">Cara Berbelanja</h4>
    </div>
    <div class="row g-4 text-center">
        <div class="col-md-3">
            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;font-size:1.4rem;font-weight:800">1</div>
            <h6 class="fw-bold">Buat Akun</h6>
            <p class="text-muted small">Daftar gratis, verifikasi Telegram agar dapat notifikasi langsung</p>
        </div>
        <div class="col-md-3">
            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;font-size:1.4rem;font-weight:800">2</div>
            <h6 class="fw-bold">Pilih Produk</h6>
            <p class="text-muted small">Browse katalog, temukan spare part yang Anda butuhkan</p>
        </div>
        <div class="col-md-3">
            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;font-size:1.4rem;font-weight:800">3</div>
            <h6 class="fw-bold">Bayar</h6>
            <p class="text-muted small">Checkout aman via Midtrans — transfer bank, QRIS, e-wallet</p>
        </div>
        <div class="col-md-3">
            <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;font-size:1.4rem;font-weight:800">4</div>
            <h6 class="fw-bold">Terima Pesanan</h6>
            <p class="text-muted small">Update pengiriman real-time via Telegram, estimasi 1–3 hari</p>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="{{ route('shop') }}" class="btn btn-primary btn-lg px-5 fw-semibold">
            <i class="bi bi-shop me-2"></i>Mulai Belanja Sekarang
        </a>
    </div>
</div>

@endsection
