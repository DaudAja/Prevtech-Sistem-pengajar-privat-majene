@extends('layouts.app')

@section('title', 'Dashboard Pelajar')

@section('content')
<div class="container-fluid p-4">
    <h1 class="text-3xl font-bold mb-5 text-gray-800">Selamat Datang, {{ $user->name }}!</h1>

    {{-- Kartu Status Singkat --}}
    <div class="row mb-5">

        {{-- Total Permintaan Pending --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stat-card warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Permintaan Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $recentRequests->where('status', 'pending')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <a href="{{ route('pelajar.permintaan.index') }}" class="card-footer bg-white text-warning text-center small text-decoration-none">
                    Lihat Riwayat Permintaan <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Total Rekomendasi Terakhir --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Rekomendasi Tersimpan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $recentRecommendations->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <a href="{{ route('pelajar.recommendations.history') }}" class="card-footer bg-white text-primary text-center small text-decoration-none">
                    Lihat Histori Rekomendasi <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Tombol Cepat Cari Pengajar --}}
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card success">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="fas fa-search fa-3x text-gray-300 mb-2"></i>
                    <h5 class="font-weight-bold text-success mb-3">Mulai Pencarian Sekarang!</h5>
                    <a href="{{ route('pelajar.search.form') }}" class="btn btn-success btn-icon-split btn-lg">
                        <span class="icon text-white-50"><i class="fas fa-search"></i></span>
                        <span class="text">Cari Pengajar Terdekat</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Main Content: Rekomendasi & Saran --}}
    <div class="row">
        {{-- Kolom Kiri: Rekomendasi Terbaru --}}
        <div class="col-lg-7 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas & Rekomendasi Terakhir</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="rec-tab" data-bs-toggle="tab" data-bs-target="#recom" type="button">Rekomendasi ({{ $recentRecommendations->count() }})</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="req-tab" data-bs-toggle="tab" data-bs-target="#request" type="button">Permintaan ({{ $recentRequests->count() }})</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">

                        {{-- Tab Rekomendasi --}}
                        <div class="tab-pane fade show active" id="recom" role="tabpanel">
                            <div class="p-3">
                                @forelse($recentRecommendations as $rec)
                                    <div class="border-bottom py-2">
                                        <a href="{{ route('pelajar.pengajar.show', $rec->pengajar->id) }}" class="font-medium text-decoration-none text-primary">
                                            {{ optional(optional($rec->pengajar)->user)->name ?? 'Pengajar Tidak Ditemukan' }}
                                        </a>
                                        <div class="text-sm text-gray-600">
                                            Jarak: <strong>{{ $rec->jarak_km }} km</strong> — Skor KNN: **{{ $rec->nilai_kemiripan }}**
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-600 mt-2">Belum ada histori rekomendasi. Coba fitur pencarian sekarang!</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Tab Permintaan --}}
                        <div class="tab-pane fade" id="request" role="tabpanel">
                            <div class="p-3">
                                @forelse($recentRequests as $p)
                                    <div class="border-bottom py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <a href="{{ route('pelajar.pengajar.show', $p->pengajar->id) }}" class="font-medium text-decoration-none text-primary">
                                                    {{ optional(optional($p->pengajar)->user)->name ?? 'Pengajar Tidak Ditemukan' }}
                                                </a>
                                                <div class="text-sm text-gray-600">Mapel: {{ $p->mata_pelajaran ?? '-' }} | Jadwal: {{ optional($p->jadwal_diinginkan)->format('d M H:i') }}</div>
                                            </div>
                                            <div class="badge badge-status
                                                @if($p->status === 'pending') bg-warning text-dark
                                                @elseif($p->status === 'diterima') bg-success
                                                @elseif($p->status === 'ditolak') bg-danger
                                                @else bg-secondary @endif text-uppercase">
                                                {{ $p->status }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-600 mt-2">Tidak ada riwayat permintaan baru. Segera cari pengajar!</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Saran Pengajar Terverifikasi --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Saran Pengajar Terverifikasi <i class="fas fa-check-circle"></i></h6>
                </div>
                <div class="card-body">
                    @forelse($verifiedPengajars as $peng)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <a href="{{ route('pelajar.pengajar.show', $peng->id) }}" class="font-medium text-decoration-none text-dark">
                                    {{ optional($peng->user)->name ?? 'N/A' }}
                                </a>
                                <div class="text-sm text-gray-600">
                                    Mapel: {{ $peng->mata_pelajaran }}
                                    • Exp: {{ $peng->pengalaman_tahun }} th
                                    <span class="rating-stars" title="Rata-rata rating: {{ $peng->average_rating }}">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star" style="color: {{ $i <= $peng->average_rating ? '#f6c23e' : '#e3e6f0' }}"></i>
                                        @endfor
                                        ({{ $peng->total_ulasan }})
                                    </span>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('pelajar.pengajar.show', $peng->id) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">Belum ada pengajar terverifikasi yang tersedia.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
