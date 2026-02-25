@extends('layouts.admin')
@section('title', 'Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Produk</h4>
    <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Produk
    </a>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.products') }}" class="mb-3 d-flex gap-2">
    <div class="input-group" style="max-width:340px">
        <span class="input-group-text" style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1)">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / brand…"
            class="form-control form-control-sm"
            style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1);color:#e2e8f0">
    </div>
    <button class="btn btn-sm btn-outline-secondary">Cari</button>
    @if(request('search'))
        <a href="{{ route('admin.products') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
    @endif
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:60px">Thumbnail</th>
                    <th>Nama Produk</th>
                    <th>Brand / Tipe</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr @if($product->trashed()) style="opacity:.5" @endif>
                    <td>
                        @if($product->thumbnail_url)
                            <img src="{{ $product->thumbnail_url }}" alt="" width="44" height="44"
                                style="object-fit:cover;border-radius:8px;border:1px solid rgba(255,255,255,.1)">
                        @else
                            <div style="width:44px;height:44px;border-radius:8px;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $product->name }}</div>
                        <small class="text-muted">{{ $product->category }}</small>
                    </td>
                    <td class="small text-muted">
                        {{ $product->brand }}
                        @if($product->type)
                            <div>{{ $product->type }}</div>
                        @endif
                    </td>
                    <td class="fw-semibold" style="color:#5dd39e;white-space:nowrap">
                        Rp {{ number_format((float)$product->price, 0, ',', '.') }}
                    </td>
                    <td>
                        @if($product->stock <= 0)
                            <span class="badge" style="background:rgba(230,57,70,0.15);color:#ff6b7a">Habis</span>
                        @elseif($product->stock <= 5)
                            <span class="badge" style="background:rgba(245,158,11,0.15);color:#fbbf24">{{ $product->stock }}</span>
                        @else
                            <span class="fw-semibold small">{{ $product->stock }}</span>
                        @endif
                    </td>
                    <td>
                        @if($product->trashed())
                            <span class="badge" style="background:rgba(107,114,128,0.15);color:#9ca3af">Dihapus</span>
                        @elseif($product->is_active)
                            <span class="badge" style="background:rgba(16,185,129,0.15);color:#34d399">Aktif</span>
                        @else
                            <span class="badge" style="background:rgba(245,158,11,0.15);color:#fbbf24">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        @if(!$product->trashed())
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="btn btn-sm btn-outline-secondary py-0 px-2">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.products.delete', $product) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"
                                        onclick="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam fs-3 d-block mb-2 opacity-50"></i>
                        Belum ada produk.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        <div class="card-footer d-flex justify-content-center py-2">
            <div class="d-flex align-items-center gap-3">
                @if($products->onFirstPage())
                    <span class="btn btn-sm btn-outline-secondary disabled">‹ Sebelumnya</span>
                @else
                    <a href="{{ $products->previousPageUrl() }}&search={{ request('search') }}" class="btn btn-sm btn-outline-secondary">‹ Sebelumnya</a>
                @endif
                <span class="text-muted small">{{ $products->currentPage() }} / {{ $products->lastPage() }}</span>
                @if($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}&search={{ request('search') }}" class="btn btn-sm btn-outline-secondary">Berikutnya ›</a>
                @else
                    <span class="btn btn-sm btn-outline-secondary disabled">Berikutnya ›</span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
