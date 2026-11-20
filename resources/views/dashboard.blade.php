@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h3 class="mb-1">Dashboard {{ ucfirst($user->role) }}</h3>
    <p class="text-muted mb-0">Ringkasan data bank sampah kampus (SDG 12).</p>
</div>

@if($user->role === 'admin')
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Jumlah Katalog Jenis Sampah</div>
                    <h2 class="fw-bold text-success mb-0">{{ $jumlahKatalog }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Transaksi Menunggu</div>
                    <h2 class="fw-bold text-warning mb-0">{{ $transaksiMenunggu }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Berat Diverifikasi (kg)</div>
                    <h2 class="fw-bold text-primary mb-0">{{ number_format($totalBeratDiverifikasi, 2, ',', '.') }} kg</h2>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small mb-1">Jumlah Transaksi Saya</div>
                    <h2 class="fw-bold text-success mb-0">{{ $jumlahTransaksiSaya }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">Daftar Transaksi Setoran Saya</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Sampah</th>
                            <th>Berat (kg)</th>
                            <th>Total Nilai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiSaya as $item)
                            <tr>
                                <td>{{ $item->tanggal_setor }}</td>
                                <td>{{ $item->jenisSampah->kode_jenis ?? '' }} - {{ $item->jenisSampah->nama_jenis ?? '' }}</td>
                                <td>{{ number_format($item->berat_kg, 2, ',', '.') }} kg</td>
                                <td>Rp{{ number_format($item->total_nilai, 2, ',', '.') }}</td>
                                <td>
                                    @if($item->status === 'Menunggu')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @elseif($item->status === 'Diverifikasi')
                                        <span class="badge bg-success">Diverifikasi</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi setoran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection