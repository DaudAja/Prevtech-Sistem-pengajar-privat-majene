@extends('layouts.app')

@section('content')

{{-- 1. CONTENT HEADER --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-tachometer-alt text-maroon"></i> Dashboard Pengajar
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard Pengajar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- 2. MAIN CONTENT --}}
<section class="content">
    <div class="container-fluid">
        {{-- INFO BOXES / WIDGETS --}}
        <div class="row">

            {{-- Widget 1: Permintaan Privat Baru --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-maroon-custom text-white">
                    <div class="inner">
                        <h3>{{ $total_permintaan_pending ?? 0 }}</h3>
                        <p>Permintaan Privat Baru</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    {{-- <a href="{{ route('pengajar.permintaan.index') }}" class="small-box-footer text-white"> --}}
                        Lihat Permintaan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            {{-- Widget 2: Total Privat Aktif --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $total_permintaan_accepted ?? 0 }}</h3>
                        <p>Total Privat Aktif</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    {{-- <a href="{{ route('pengajar.requests.history') }}" class="small-box-footer"> --}}
                        Lihat Riwayat <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            {{-- Widget 3: Rata-rata Rating --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($avg_rating ?? 0, 1) }}<sup style="font-size: 20px">/5</sup></h3>
                        <p>Rata-rata Rating</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-star"></i>
                    </div>
                    {{-- <a href="{{ route('pengajar.ulasan.index') }}" class="small-box-footer"> --}}
                        Detail Ulasan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            {{-- Widget 4: Total Ulasan --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $total_ulasan ?? 0 }}</h3>
                        <p>Total Ulasan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    {{-- <a href="{{ route('pengajar.ulasan.index') }}" class="small-box-footer"> --}}
                        Lihat Semua <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

        </div>
        {{-- /.row --}}

        {{-- ROW UTAMA KONTEN --}}
        <div class="row">

            {{-- KIRI: PERMINTAAN TERBARU --}}
            <section class="col-lg-7">
                <div class="card">
                    <div class="card-header border-0 bg-maroon-custom text-white">
                        <h3 class="card-title">
                            <i class="fas fa-stream"></i> Permintaan Privat Terbaru
                        </h3>
                        <div class="card-tools">
                            {{-- <a href="{{ route('pengajar.permintaan.index') }}" class="btn btn-sm btn-outline-light"> --}}
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if(!empty($permintaan_terbaru) && count($permintaan_terbaru) > 0)
                            <ul class="products-list product-list-in-card pl-2 pr-2">
                                {{-- Loop permintaan terbaru --}}
                                @foreach($permintaan_terbaru as $permintaan)
                                <li class="item">
                                    <div class="product-info">
                                        <a href="{{ route('pengajar.permintaan.show', $permintaan->id) }}" class="product-title text-maroon">
                                            Permintaan dari {{ $permintaan->pelajar->user->name }}
                                            <span class="badge badge-warning float-right">{{ ucfirst($permintaan->status) }}</span>
                                        </a>
                                        <span class="product-description">
                                            Jadwal: {{ \Carbon\Carbon::parse($permintaan->jadwal_diinginkan)->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="p-3 text-center text-muted">
                                Tidak ada permintaan privat yang tertunda.
                            </div>
                        @endif
                    </div>
                </div>
            </section>
            {{-- /.LEFT COL --}}

            {{-- KANAN: STATUS PROFIL --}}
            <section class="col-lg-5">
                <div class="card">
                    <div class="card-header bg-maroon-custom text-white">
                        <h3 class="card-title"><i class="fas fa-user-tie"></i> Status Profil Anda</h3>
                    </div>
                    <div class="card-body box-profile">
                        {{-- <div class="text-center mb-3">
                            @if(optional(Auth::user()->pengajar)->foto_profil)
                                <img class="profile-user-img img-fluid img-circle"
                                     src="{{ asset('storage/'.Auth::user()->pengajar->foto_profil) }}"
                                     alt="Foto Profil">
                            @else
                                <img class="profile-user-img img-fluid img-circle"
                                     src="{{ asset('img/default-user.png') }}"
                                     alt="Default Foto">
                            @endif
                        </div> --}}

                        <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Status Verifikasi</b>
                                {{-- <span class="float-right">
                                    @if($pengajar_profile->status_verifikasi)
                                        <span class="badge badge-success">Terverifikasi</span>
                                    @else
                                        <span class="badge badge-danger">Belum</span>
                                    @endif
                                </span> --}}
                            </li>
                            <li class="list-group-item">
                                <b>Mata Pelajaran</b>
                                <span class="float-right text-muted">{{ $pengajar_profile->mata_pelajaran ?? '-' }}</span>
                            </li>
                            {{-- <li class="list-group-item">
                                <b>Tarif</b>
                                <span class="float-right text-muted">Rp{{ number_format($pengajar_profile->tarif_per_jam ?? 0, 0, ',', '.') }}/Jam</span>
                            </li> --}}
                        </ul>

                        {{-- <a href="{{ route('pengajar.profile.edit') }}" class="btn bg-maroon-custom btn-block text-white"> --}}
                            <i class="fas fa-edit"></i> **Edit Profil Saya**
                        </a>
                    </div>
                    {{-- /.card-body --}}
                </div>
            </section>
            {{-- /.RIGHT COL --}}

        </div>
        {{-- /.row (main row) --}}

    </div></section>
@endsection

@push('scripts')
{{-- Tambahkan script yang diperlukan untuk AdminLTE jika ada chart, dll. --}}

<script>
    // Contoh untuk memastikan widget status profil menggunakan warna maroon pada border card
    document.addEventListener('DOMContentLoaded', function() {
        const primaryCard = document.querySelector('.card-outline.card-primary');
        if (primaryCard) {
            primaryCard.style.borderTopColor = '#800000';
        }
    });
</script>
@endpush
