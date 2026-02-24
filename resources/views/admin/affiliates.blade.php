@extends('layouts.admin')
@section('title', 'Affiliates')

@section('content')
<h4 class="fw-bold mb-4">Daftar Affiliate</h4>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Kode Referral</th>
                    <th>Clicks</th>
                    <th>Konversi</th>
                    <th>Conversion Rate</th>
                    <th>Total Komisi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($affiliates as $aff)
                @php
                    $rate = $aff->referral_clicks_count > 0
                        ? round(($aff->conversions_count / $aff->referral_clicks_count) * 100, 1)
                        : 0;
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $aff->user?->name ?? '-' }}</div>
                        <small class="text-muted">{{ $aff->user?->email }}</small>
                    </td>
                    <td><code>{{ $aff->referral_code }}</code></td>
                    <td>{{ $aff->referral_clicks_count }}</td>
                    <td>{{ $aff->conversions_count }}</td>
                    <td>{{ $rate }}%</td>
                    <td class="text-success fw-semibold">Rp {{ number_format($aff->total_commission_amount, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-{{ $aff->status === 'active' ? 'success' : 'secondary' }}">
                            {{ $aff->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">Belum ada affiliate</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $affiliates->links() }}</div>
</div>
@endsection
