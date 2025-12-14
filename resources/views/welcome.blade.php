<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengajar Privat Majene</title>

    {{-- Menggunakan aset lokal AdminLTE/Bootstrap 4 --}}
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    <style>
        /* Definisi Warna Maroon Kustom */
        .bg-maroon-gradient {
            /* Gradient dari Deep Maroon ke Maroon yang lebih cerah/hangat */
            background: linear-gradient(135deg, #800000 0%, #A52A2A 100%) !important;
        }
        .text-maroon {
            color: #800000 !important;
        }
        .bg-maroon-custom {
            background-color: #800000 !important;
        }

        /* Gaya Hero Section */
        .hero-section {
            /* Mengganti background dengan gradient maroon */
            background: linear-gradient(135deg, #800000 0%, #A52A2A 100%);
            color: white;
            padding: 100px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        /* Mengganti warna icon di Hero agar tetap kontras */
        .hero-section .fa-graduation-cap {
            color: #fff; /* Tetap putih */
        }

        /* Gaya Card Fitur */
        .feature-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 3rem;
            /* Mengganti warna icon fitur menjadi maroon */
            color: #800000;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    {{-- Hero Section --}}
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 font-weight-bold mb-4">Temukan Pengajar Privat Terbaik di Majene</h1>
                    <p class="lead mb-4">Sistem rekomendasi cerdas menggunakan algoritma K-Nearest Neighbor untuk mencocokkan Anda dengan pengajar terbaik berdasarkan lokasi terdekat.</p>
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="{{ route('register') }}" class="btn bg-white btn-lg px-4 mr-2 text-maroon">
                            <i class="fas fa-user-plus"></i> Daftar Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-graduation-cap" style="font-size: 15rem; opacity: 0.15; color: white;"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 font-weight-light text-maroon">Fitur Unggulan</h2>
            <div class="row g-4">
                {{-- Feature Card 1 --}}
                <div class="col-md-4">
                    <div class="card feature-card h-100 shadow-sm border-top-0 border-left-0 border-right-0 border-maroon-custom" style="border-width: 3px !important;">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-map-marker-alt feature-icon"></i>
                            <h5 class="card-title font-weight-bold">Pencarian Berbasis Lokasi</h5>
                            <p class="card-text text-muted">Temukan pengajar terdekat dari lokasi Anda menggunakan algoritma KNN yang akurat.</p>
                        </div>
                    </div>
                </div>
                {{-- Feature Card 2 --}}
                <div class="col-md-4">
                    <div class="card feature-card h-100 shadow-sm border-top-0 border-left-0 border-right-0 border-maroon-custom" style="border-width: 3px !important;">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-star feature-icon"></i>
                            <h5 class="card-title font-weight-bold">Sistem Rating & Ulasan</h5>
                            <p class="card-text text-muted">Lihat rating dan ulasan dari pelajar lain untuk memilih pengajar terbaik.</p>
                        </div>
                    </div>
                </div>
                {{-- Feature Card 3 --}}
                <div class="col-md-4">
                    <div class="card feature-card h-100 shadow-sm border-top-0 border-left-0 border-right-0 border-maroon-custom" style="border-width: 3px !important;">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-user-check feature-icon"></i>
                            <h5 class="card-title font-weight-bold">Pengajar Terverifikasi</h5>
                            <p class="card-text text-muted">Semua pengajar telah melalui proses verifikasi untuk memastikan kualitas.</p>
                        </div>
                    </div>
                </div>
                {{-- Feature Card 4 --}}
                <div class="col-md-4">
                    <div class="card feature-card h-100 shadow-sm border-top-0 border-left-0 border-right-0 border-maroon-custom" style="border-width: 3px !important;">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-book feature-icon"></i>
                            <h5 class="card-title font-weight-bold">Beragam Mata Pelajaran</h5>
                            <p class="card-text text-muted">Matematika, Fisika, Bahasa Inggris, Pemrograman, dan banyak lagi.</p>
                        </div>
                    </div>
                </div>
                {{-- Feature Card 5 --}}
                <div class="col-md-4">
                    <div class="card feature-card h-100 shadow-sm border-top-0 border-left-0 border-right-0 border-maroon-custom" style="border-width: 3px !important;">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-calendar-check feature-icon"></i>
                            <h5 class="card-title font-weight-bold">Jadwal Fleksibel</h5>
                            <p class="card-text text-muted">Atur jadwal belajar sesuai ketersediaan Anda dan pengajar.</p>
                        </div>
                    </div>
                </div>
                {{-- Feature Card 6 --}}
                <div class="col-md-4">
                    <div class="card feature-card h-100 shadow-sm border-top-0 border-left-0 border-right-0 border-maroon-custom" style="border-width: 3px !important;">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-mobile-alt feature-icon"></i>
                            <h5 class="card-title font-weight-bold">Mudah Digunakan</h5>
                            <p class="card-text text-muted">Interface yang user-friendly dan responsif di semua perangkat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5 font-weight-light text-maroon">Cara Kerja</h2>
            <div class="row">
                {{-- Step 1 --}}
                <div class="col-md-3 text-center mb-4">
                    {{-- Ganti bg-primary menjadi bg-maroon-custom --}}
                    <div class="rounded-circle bg-maroon-custom text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        1
                    </div>
                    <h5 class="mt-3 font-weight-bold">Daftar</h5>
                    <p class="text-muted">Buat akun sebagai pelajar atau pengajar</p>
                </div>
                {{-- Step 2 --}}
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-maroon-custom text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        2
                    </div>
                    <h5 class="mt-3 font-weight-bold">Cari Pengajar</h5>
                    <p class="text-muted">Input kriteria dan lokasi Anda</p>
                </div>
                {{-- Step 3 --}}
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-maroon-custom text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        3
                    </div>
                    <h5 class="mt-3 font-weight-bold">Pilih & Hubungi</h5>
                    <p class="text-muted">Lihat rekomendasi dan hubungi pengajar</p>
                </div>
                {{-- Step 4 --}}
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-maroon-custom text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        4
                    </div>
                    <h5 class="mt-3 font-weight-bold">Mulai Belajar</h5>
                    <p class="text-muted">Atur jadwal dan mulai pembelajaran</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-5 bg-maroon-custom text-white">
        <div class="container text-center">
            <h2 class="mb-4 font-weight-bold">Siap Meningkatkan Prestasi Akademik Anda?</h2>
            <p class="lead mb-4">Bergabunglah dengan ratusan pelajar dan pengajar di Majene</p>
            <a href="{{ route('register') }}" class="btn btn-warning btn-lg">
                <i class="fas fa-rocket"></i> Mulai Sekarang
            </a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-4 bg-dark text-white text-center">
        <div class="container">
            <p class="mb-0">&copy; 2025 Sistem Pengajar Privat Majene. Universitas Sulawesi Barat.</p>
            <p class="mb-0 mt-2">
                <small>Dikembangkan dengan <i class="fas fa-heart text-danger"></i> untuk pendidikan yang lebih baik</small>
            </p>
        </div>
    </footer>

    {{-- Aset JavaScript AdminLTE/Bootstrap 4 --}}
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
</body>
</html>
