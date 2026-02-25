@extends('layouts.app')
@section('title', 'Beranda - TDR HPZ Store')

@push('styles')
<style>
.hero-landing {
    background: linear-gradient(165deg, #0b0b0f 0%, #1a1a2e 40%, #16213e 100%);
    padding: 80px 0 60px;
    position: relative;
    overflow: hidden;
}
.hero-landing::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(230,57,70,0.08) 0%, transparent 70%);
    border-radius: 50%;
}
.hero-landing::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(212,168,67,0.06) 0%, transparent 70%);
    border-radius: 50%;
}
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(230,57,70,0.1);
    border: 1px solid rgba(230,57,70,0.2);
    color: var(--tdr-red);
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
}
.stat-hero { text-align: center; }
.stat-hero .num {
    font-size: 2.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--tdr-gold) 0%, #f0d078 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.1;
}
.stat-hero .lbl { font-size: 0.825rem; color: var(--tdr-muted); }
.trust-box {
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--tdr-border);
    border-radius: 12px;
    padding: 20px;
    backdrop-filter: blur(8px);
}
.trust-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.feature-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    margin: 0 auto 14px;
}
.product-card-sm {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
}
.product-card-sm:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3) !important;
}
.product-placeholder {
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.03);
    color: var(--tdr-muted);
    font-size: 2rem;
}
.testimonial-card {
    border-left: 3px solid var(--tdr-red) !important;
}
.section-title {
    font-weight: 800;
    font-size: 1.5rem;
    letter-spacing: -0.3px;
}
.section-subtitle {
    color: var(--tdr-muted);
    font-size: 0.9rem;
}
.step-circle {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: 800;
    margin-bottom: 12px;
}
.star-rating { color: var(--tdr-gold); font-size: 0.85rem; letter-spacing: 2px; }
.cta-section {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0b2027 100%);
    border-top: 1px solid var(--tdr-border);
    border-bottom: 1px solid var(--tdr-border);
}
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="hero-landing">
    <div class="container position-relative" style="z-index:1">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-lg-start">
                <div class="hero-badge mb-3">
                    <i class="bi bi-shield-check"></i> Terpercaya sejak 2020
                </div>
                <h1 class="fw-bold display-4 mb-3" style="letter-spacing:-1px">
                    Spare Part Motor <span style="color:var(--tdr-gold)">Berkualitas</span>
                </h1>
                <p class="lead mb-4" style="color:var(--tdr-muted);font-size:1.05rem;line-height:1.7">
                    TDR HPZ — distributor resmi spare part motor terbaik.
                    Harga kompetitif, stok lengkap, pengiriman ke seluruh Indonesia.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="{{ route('shop') }}" class="btn btn-primary btn-lg fw-bold px-5">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Lihat Produk
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4" style="border-color:rgba(255,255,255,0.15)">
                        Daftar Gratis
                    </a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="row g-3 mb-4">
                    <div class="col-4 stat-hero">
                        <div class="num">500+</div>
                        <div class="lbl">Produk tersedia</div>
                    </div>
                    <div class="col-4 stat-hero">
                        <div class="num">10K+</div>
                        <div class="lbl">Pelanggan puas</div>
                    </div>
                    <div class="col-4 stat-hero">
                        <div class="num">4.9</div>
                        <div class="lbl">Rating toko</div>
                    </div>
                </div>
                <div class="trust-box">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="trust-icon" style="background:rgba(25,135,84,0.15);color:#5dd39e">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem">Pembayaran Aman</div>
                            <small style="color:var(--tdr-muted)">Dijamin via Midtrans / Snap</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="trust-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem">Pengiriman Cepat</div>
                            <small style="color:var(--tdr-muted)">JNE / J&T / SiCepat — terlacak real-time</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FEATURED PRODUCTS --}}
@if($featuredProducts->isNotEmpty())
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="section-title mb-1">Produk Unggulan</h3>
            <p class="section-subtitle mb-0">Pilihan terlaris dari katalog kami</p>
        </div>
        <a href="{{ route('shop') }}" class="btn btn-outline-primary">
            Semua Produk <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="row g-4">
        @foreach($featuredProducts as $product)
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100 product-card-sm">
                <div class="product-placeholder"><i class="bi bi-box-seam"></i></div>
                <div class="card-body p-2">
                    <div class="fw-semibold" style="line-height:1.3;font-size:.8rem">{{ \Illuminate\Support\Str::limit($product->name, 35) }}</div>
                    <div class="fw-bold small mt-1" style="color:var(--tdr-red)">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                </div>
                <div class="card-footer border-0 p-2 pt-0">
                    <a href="{{ route('shop') }}" class="btn btn-primary btn-sm w-100" style="font-size:.75rem">Beli</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<hr class="my-0" style="border-color:var(--tdr-border)">
@endif

{{-- WHY US --}}
<div class="container py-5">
    <div class="text-center mb-5">
        <h3 class="section-title">Kenapa Pilih TDR HPZ?</h3>
        <p class="section-subtitle">Kami berkomitmen memberikan pengalaman belanja terbaik untuk kebutuhan motor Anda</p>
    </div>
    <div class="row g-4 text-center">
        <div class="col-6 col-md-3">
            <div class="feature-icon" style="background:rgba(230,57,70,0.1);color:var(--tdr-red)">
                <i class="bi bi-award-fill"></i>
            </div>
            <h6 class="fw-bold">Produk Original</h6>
            <p class="text-muted small">100% produk asli bersertifikat, kami tidak jual produk KW</p>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-icon" style="background:rgba(16,185,129,0.1);color:#34d399">
                <i class="bi bi-truck"></i>
            </div>
            <h6 class="fw-bold">Pengiriman Cepat</h6>
            <p class="text-muted small">Same-day processing, estimasi 1-3 hari ke seluruh Indonesia</p>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-icon" style="background:rgba(59,130,246,0.1);color:#60a5fa">
                <i class="bi bi-bell-fill"></i>
            </div>
            <h6 class="fw-bold">Notifikasi Real-Time</h6>
            <p class="text-muted small">Update status pesanan langsung ke Telegram Anda, 24/7</p>
        </div>
        <div class="col-6 col-md-3">
            <div class="feature-icon" style="background:rgba(212,168,67,0.1);color:var(--tdr-gold)">
                <i class="bi bi-lock-fill"></i>
            </div>
            <h6 class="fw-bold">Pembayaran Aman</h6>
            <p class="text-muted small">Diproses via Midtrans — bank transfer, QRIS & e-wallet</p>
        </div>
    </div>
</div>

{{-- TESTIMONIALS --}}
<div style="background:rgba(255,255,255,0.02);border-top:1px solid var(--tdr-border);border-bottom:1px solid var(--tdr-border)" class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h4 class="section-title">Apa Kata Pelanggan Kami</h4>
        </div>
        <div class="row g-4">
            @foreach([
                ['Budi Santoso',    'Mekanik Bengkel',     5, 'Sudah 3 tahun langganan di sini. Kualitas spare part selalu konsisten dan harga jauh lebih murah dari toko offline.'],
                ['Siti Rahayu',     'Pengguna Honda Beat', 5, 'Pemesanan gampang, langsung beli dan bayar. Notif Telegram-nya keren, bisa tahu status pesanan real-time!'],
                ['Dimas Prasetyo',  'Mekanik Freelance',   5, 'Program affiliate TDR-HPZ bantu penghasilan tambahan. Komisi 10% cair otomatis tanpa ribet follow-up admin.'],
            ] as [$name, $role, $stars, $text])
            <div class="col-md-4">
                <div class="card p-4 testimonial-card h-100">
                    <div class="star-rating mb-2">
                        @for($i = 0; $i < $stars; $i++)
                            <i class="bi bi-star-fill"></i>
                        @endfor
                    </div>
                    <p class="text-muted small mb-3 flex-grow-1">{{ $text }}</p>
                    <div class="fw-semibold">{{ $name }}</div>
                    <small class="text-muted">{{ $role }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- AFFILIATE CTA --}}
<div class="py-5 cta-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7 mb-4 mb-md-0">
                <div class="hero-badge mb-3">
                    <i class="bi bi-currency-dollar"></i> Program Affiliate
                </div>
                <h3 class="fw-bold">Hasilkan Uang dari Referral Anda</h3>
                <ul class="list-unstyled mt-3 mb-0" style="color:var(--tdr-muted)">
                    <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--tdr-gold)"></i> Komisi 10% dari setiap pembelian via link Anda</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color:var(--tdr-gold)"></i> Notifikasi otomatis via Telegram saat komisi masuk</li>
                    <li><i class="bi bi-check-circle-fill me-2" style="color:var(--tdr-gold)"></i> Pencairan ke rekening bank / e-wallet (OVO, DANA, GoPay)</li>
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
                            <i class="bi bi-bar-chart-line me-2"></i>Dashboard Saya
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold px-5 mb-2">
                        <i class="bi bi-people-fill me-2"></i>Daftar & Jadi Affiliate
                    </a>
                    <p class="text-muted small mt-2 mb-0">Gratis! Persetujuan admin 1x24 jam</p>
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- HOW TO ORDER --}}
<div class="container py-5">
    <div class="text-center mb-4">
        <h4 class="section-title">Cara Berbelanja</h4>
    </div>
    <div class="row g-4 text-center">
        <div class="col-md-3">
            <div class="step-circle" style="background:rgba(230,57,70,0.1);color:var(--tdr-red)">1</div>
            <h6 class="fw-bold">Buat Akun</h6>
            <p class="text-muted small">Daftar gratis, verifikasi Telegram agar dapat notifikasi langsung</p>
        </div>
        <div class="col-md-3">
            <div class="step-circle" style="background:rgba(59,130,246,0.1);color:#60a5fa">2</div>
            <h6 class="fw-bold">Pilih Produk</h6>
            <p class="text-muted small">Browse katalog, temukan spare part yang Anda butuhkan</p>
        </div>
        <div class="col-md-3">
            <div class="step-circle" style="background:rgba(212,168,67,0.1);color:var(--tdr-gold)">3</div>
            <h6 class="fw-bold">Bayar</h6>
            <p class="text-muted small">Checkout aman via Midtrans — transfer bank, QRIS, e-wallet</p>
        </div>
        <div class="col-md-3">
            <div class="step-circle" style="background:rgba(16,185,129,0.1);color:#34d399">4</div>
            <h6 class="fw-bold">Terima Pesanan</h6>
            <p class="text-muted small">Update pengiriman real-time via Telegram, estimasi 1-3 hari</p>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="{{ route('shop') }}" class="btn btn-primary btn-lg px-5 fw-semibold">
            <i class="bi bi-grid-3x3-gap me-2"></i>Mulai Belanja Sekarang
        </a>
    </div>
</div>

@endsection
