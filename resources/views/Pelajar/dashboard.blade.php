@extends('layouts.app')

@section('title', 'Dashboard Pelajar')

@section('content')
{{-- Hapus div class="content-wrapper" di sini, karena sudah ada di layouts/app.blade.php --}}

{{-- Hapus p-4 pada class="content" karena container-fluid sudah memiliki padding --}}
<section class="content">
    <div class="container-fluid">

        {{-- Row untuk Judul --}}
        <div class="row pt-3"> {{-- Tambahkan pt-3 (padding top) untuk memberi sedikit jarak dari navbar --}}
            <div class="col-12">
                {{-- Gunakan h4/h3 dan mb-3 (margin bottom 3) --}}
                <h3 class="mb-3 text-gray-800">
                    <i class="fas fa-home me-2"></i> Dashboard Pelajar
                </h3>
                <p class="text-muted">Selamat Datang, <strong>{{ optional($user)->name }}</strong>!</p>
            </div>
        </div>

        {{-- Kartu Status Singkat (Small Boxes) --}}
        <div class="row">
            {{-- Hapus mb-5 (margin bottom 5) dari sini --}}

            {{-- Total Permintaan Pending --}}
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $recentRequests->where('status', 'pending')->count() }}</h3>
                        <p>Permintaan Privat Pending</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('pelajar.permintaan.index') }}" class="small-box-footer">
                        Lihat Riwayat <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            {{-- Total Rekomendasi Terakhir --}}
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $recentRecommendations->count() }}</h3>
                        <p>Histori Rekomendasi Tersimpan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <a href="{{ route('pelajar.recommendations.history') }}" class="small-box-footer">
                        Lihat Histori <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            {{-- Tombol Cepat Cari Pengajar --}}
            <div class="col-lg-4 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>Pencarian</h3>
                        <p>Cari Pengajar Terdekat Sekarang!</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <a href="{{ route('pelajar.search.form') }}" class="small-box-footer">
                        Mulai Pencarian <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

        </div>

        {{-- Main Content: Rekomendasi & Saran --}}
        <div class="row">
             {{-- Hapus mb-5/mt-4/mb-4 dari div ini, biarkan AdminLTE mengatur gap antar row --}}

            {{-- Kolom Kiri: Aktivitas Terbaru (Menggunakan Card AdminLTE) --}}
            <div class="col-lg-7">
                <div class="card card-primary card-outline">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Aktivitas & Rekomendasi Terbaru</h3>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            {{-- Gunakan data-toggle="tab" dan data-target="#id" (Bootstrap 4/AdminLTE) --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="rec-tab" data-toggle="tab" data-target="#recom" type="button">Rekomendasi ({{ $recentRecommendations->count() }})</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="req-tab" data-toggle="tab" data-target="#request" type="button">Permintaan ({{ $recentRequests->count() }})</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">

                            {{-- Tab Rekomendasi --}}
                            <div class="tab-pane fade show active" id="recom" role="tabpanel">
                                <div class="p-3">
                                    @forelse($recentRecommendations as $rec)
                                        <div class="pb-2 mb-2 border-bottom">
                                            <a href="{{ route('pelajar.pengajar.show', $rec->pengajar->id) }}" class="font-weight-bold text-decoration-none text-primary">
                                                {{ optional(optional($rec->pengajar)->user)->name ?? 'Pengajar Tidak Ditemukan' }}
                                            </a>
                                            <div class="text-sm text-muted">
                                                Jarak: <strong>{{ $rec->jarak_km }} km</strong> — Skor KNN: <span class="badge bg-primary">{{ $rec->nilai_kemiripan }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-muted mt-2">Belum ada histori rekomendasi. Coba fitur pencarian sekarang!</p>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Tab Permintaan --}}
                            <div class="tab-pane fade" id="request" role="tabpanel">
                                <div class="p-3">
                                    @forelse($recentRequests as $p)
                                        <div class="pb-2 mb-2 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <a href="{{ route('pelajar.pengajar.show', $p->pengajar->id) }}" class="font-weight-bold text-decoration-none text-primary">
                                                        {{ optional(optional($p->pengajar)->user)->name ?? 'Pengajar Tidak Ditemukan' }}
                                                    </a>
                                                    <div class="text-sm text-muted">Mapel: {{ $p->mata_pelajaran ?? '-' }} | Jadwal: {{ optional($p->jadwal_diinginkan)->format('d M H:i') }}</div>
                                                </div>
                                                <span class="badge
                                                    @if($p->status === 'pending') bg-warning
                                                    @elseif($p->status === 'diterima') bg-success
                                                    @elseif($p->status === 'ditolak') bg-danger
                                                    @else bg-secondary @endif text-uppercase">
                                                    {{ $p->status }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-muted mt-2">Tidak ada riwayat permintaan baru. Segera cari pengajar!</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Saran Pengajar Terverifikasi (Menggunakan Card AdminLTE) --}}
            <div class="col-lg-5">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title text-white">Saran Pengajar Terverifikasi <i class="fas fa-check-circle"></i></h3>
                    </div>
                    <div class="card-body">
                        @forelse($verifiedPengajars as $peng)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <div>
                                    <a href="{{ route('pelajar.pengajar.show', $peng->id) }}" class="font-weight-bold text-dark text-decoration-none">
                                        {{ optional($peng->user)->name ?? 'N/A' }}
                                    </a>
                                    <div class="text-sm text-muted">
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
                                    <a href="{{ route('pelajar.pengajar.show', $peng->id) }}" class="btn btn-sm btn-outline-primary">Lihat Profil</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-muted">Belum ada pengajar terverifikasi yang tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- Hapus div class="content-wrapper" penutup di sini --}}
@endsection
