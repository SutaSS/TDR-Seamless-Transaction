@extends('layouts.admin')
@section('title', $product ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.products') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0">{{ $product ? 'Edit Produk' : 'Tambah Produk' }}</h4>
</div>

<form method="POST"
    action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if($product) @method('PUT') @endif

    <div class="row g-4">
        {{-- Left column --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Informasi Produk</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product?->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Contoh: Knalpot Racing HPZ Series-X"
                            style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#e2e8f0">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand', $product?->brand) }}"
                                class="form-control form-control-sm"
                                style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#e2e8f0"
                                placeholder="HPZ, TDR, FFA…">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Tipe</label>
                            <input type="text" name="type" value="{{ old('type', $product?->type) }}"
                                class="form-control form-control-sm"
                                style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#e2e8f0"
                                placeholder="Racing, Standar…">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Kategori</label>
                            <select name="category" class="form-select form-select-sm"
                                style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#e2e8f0">
                                @foreach(['Rantai','Shockbreaker','Kampas Rem','Gear Sprocket','Oli','Aksesoris','Motor','Lainnya'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $product?->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <textarea name="description" rows="5"
                            class="form-control"
                            style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#e2e8f0"
                            placeholder="Deskripsi lengkap produk…">{{ old('description', $product?->description) }}</textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Spesifikasi Teknis</label>
                        <textarea name="technical_specs" rows="4"
                            class="form-control"
                            style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#e2e8f0"
                            placeholder="Material, dimensi, kompatibilitas motor…">{{ old('technical_specs', $product?->technical_specs) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Media</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">URL Thumbnail</label>
                        <input type="url" name="thumbnail_url" value="{{ old('thumbnail_url', $product?->thumbnail_url) }}"
                            class="form-control form-control-sm @error('thumbnail_url') is-invalid @enderror"
                            style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#e2e8f0"
                            placeholder="https://…">
                        @error('thumbnail_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">URL Video (YouTube embed / direct)</label>
                        <input type="url" name="master_video_url" value="{{ old('master_video_url', $product?->master_video_url) }}"
                            class="form-control form-control-sm @error('master_video_url') is-invalid @enderror"
                            style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#e2e8f0"
                            placeholder="https://youtube.com/…">
                        @error('master_video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Harga &amp; Stok</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $product?->price) }}"
                            min="0" step="1000"
                            class="form-control @error('price') is-invalid @enderror"
                            style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#e2e8f0"
                            placeholder="0">
                        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}"
                            min="0"
                            class="form-control @error('stock') is-invalid @enderror"
                            style="background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.15);color:#e2e8f0">
                        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Status</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                            {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="isActive">Produk Aktif (tampil di toko)</label>
                    </div>
                </div>
            </div>

            @if($product?->thumbnail_url)
            <div class="card mb-4">
                <div class="card-body p-3">
                    <div class="small text-muted mb-2">Preview Thumbnail</div>
                    <img src="{{ $product->thumbnail_url }}" alt="" class="w-100"
                        style="border-radius:8px;object-fit:cover;max-height:180px">
                </div>
            </div>
            @endif

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    {{ $product ? 'Simpan Perubahan' : 'Tambah Produk' }}
                </button>
                <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
