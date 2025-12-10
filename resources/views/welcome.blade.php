<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengajar Privat Majene</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .feature-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">Temukan Pengajar Privat Terbaik di Majene</h1>
                    <p class="lead mb-4">Sistem rekomendasi cerdas menggunakan algoritma K-Nearest Neighbor untuk mencocokkan Anda dengan pengajar terbaik berdasarkan lokasi, keahlian, dan kebutuhan Anda.</p>
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4 me-md-2">
                            <i class="fas fa-user-plus"></i> Daftar Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-graduation-cap" style="font-size: 15rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Fitur Unggulan</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-map-marker-alt feature-icon"></i>
                            <h5 class="card-title">Pencarian Berbasis Lokasi</h5>
                            <p class="card-text">Temukan pengajar terdekat dari lokasi Anda menggunakan algoritma KNN yang akurat.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-star feature-icon"></i>
                            <h5 class="card-title">Sistem Rating & Ulasan</h5>
                            <p class="card-text">Lihat rating dan ulasan dari pelajar lain untuk memilih pengajar terbaik.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-user-check feature-icon"></i>
                            <h5 class="card-title">Pengajar Terverifikasi</h5>
                            <p class="card-text">Semua pengajar telah melalui proses verifikasi untuk memastikan kualitas.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-book feature-icon"></i>
                            <h5 class="card-title">Beragam Mata Pelajaran</h5>
                            <p class="card-text">Matematika, Fisika, Bahasa Inggris, Pemrograman, dan banyak lagi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-calendar-check feature-icon"></i>
                            <h5 class="card-title">Jadwal Fleksibel</h5>
                            <p class="card-text">Atur jadwal belajar sesuai ketersediaan Anda dan pengajar.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-mobile-alt feature-icon"></i>
                            <h5 class="card-title">Mudah Digunakan</h5>
                            <p class="card-text">Interface yang user-friendly dan responsif di semua perangkat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Cara Kerja</h2>
            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        1
                    </div>
                    <h5 class="mt-3">Daftar</h5>
                    <p>Buat akun sebagai pelajar atau pengajar</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        2
                    </div>
                    <h5 class="mt-3">Cari Pengajar</h5>
                    <p>Input kriteria dan lokasi Anda</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        3
                    </div>
                    <h5 class="mt-3">Pilih & Hubungi</h5>
                    <p>Lihat rekomendasi dan hubungi pengajar</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        4
                    </div>
                    <h5 class="mt-3">Mulai Belajar</h5>
                    <p>Atur jadwal dan mulai pembelajaran</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-4">Siap Meningkatkan Prestasi Akademik Anda?</h2>
            <p class="lead mb-4">Bergabunglah dengan ratusan pelajar dan pengajar di Majene</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                <i class="fas fa-rocket"></i> Mulai Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 bg-dark text-white text-center">
        <div class="container">
            <p class="mb-0">&copy; 2025 Sistem Pengajar Privat Majene. Universitas Sulawesi Barat.</p>
            <p class="mb-0 mt-2">
                <small>Dikembangkan dengan <i class="fas fa-heart text-danger"></i> untuk pendidikan yang lebih baik</small>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
