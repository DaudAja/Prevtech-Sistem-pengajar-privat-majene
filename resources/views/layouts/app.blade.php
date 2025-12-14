<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengajar Privat')</title>

    {{-- 1. ADMINLTE / BOOTSTRAP 4 ASSETS --}}
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    {{-- Leaflet CSS (KOREKSI PATH KRITIS: HARUS: https://unpkg.com/leaflet@1.9.4/dist/leaflet.css) --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @stack('styles')
    <style>
        /* ---------------------------------------------------- */
        /* OVERRIDE TEMA MAROON & SIDEBAR TERANG */
        /* ---------------------------------------------------- */
        /* ... (CSS Maroon Anda sebelumnya di sini) ... */

        /* --- PERBAIKAN KRITIS UNTUK LEAFLET TILE ISSUE --- */
        /* Mencegah Bootstrap/AdminLTE mengganggu gambar peta */
        .leaflet-container img {
            /* Wajib: Mengatasi konflik max-width Bootstrap */
            max-width: none !important;
            /* Memastikan gambar dirender sebagai blok inline untuk mencegah gap/offset */
            display: inline !important;
        }

        .leaflet-container {
            /* Memastikan peta terlihat di atas elemen lain */
            z-index: 1;
        }

        /* ... (Penutup CSS lainnya) ... */

        :root {
            /* Definisikan warna dasar Maroon */
            --maroon: #800000;
            --maroon-dark: #660000;
        }

        /* Override untuk Warna Primary di Kelas AdminLTE */
        .bg-primary,
        .btn-primary,
        .card-primary>.card-header,
        .widget-user-2 .widget-user-header,
        .widget-user .widget-user-header {
            background-color: var(--maroon) !important;
        }

        /* Override untuk Tombol dan Aksi */
        .btn-primary {
            border-color: var(--maroon) !important;
        }

        .btn-primary:hover {
            background-color: var(--maroon-dark) !important;
            border-color: var(--maroon-dark) !important;
        }

        /* Warna Text dan Link Primary */
        .text-primary,
        a.text-primary:hover {
            color: var(--maroon) !important;
        }

        /* --- PERBAIKAN UNTUK SIDEBAR TERANG (sidebar-light-primary) --- */

        /* Warna Link Aktif di Sidebar Terang: Maroon */
        .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active,
        .sidebar-light-primary .nav-treeview>.nav-item>.nav-link.active {
            background-color: var(--maroon) !important;
            color: #fff !important;
            /* Teks putih di atas background maroon */
        }

        /* Hover dan Link Aktif di Sidebar Terang menggunakan Maroon */
        .sidebar-light-primary .nav-treeview>.nav-item>.nav-link:hover {
            background-color: rgba(128, 0, 0, 0.1);
            /* Maroon transparan saat hover */
            color: var(--maroon) !important;
            /* Teks Maroon saat hover */
        }

        /* Brand Link (Logo) */
        .brand-link {
            /* Di sidebar terang, kita pastikan teks logo berwarna Maroon */
            color: var(--maroon) !important;
            border-bottom: 1px solid #dee2e6;
            /* Border tipis di bawah logo */
        }

        .brand-link:hover {
            background-color: #f8f9fa;
            /* Hover background putih */
        }

        /* Warna Navbar Brand */
        .navbar-brand {
            color: var(--maroon) !important;
            font-weight: bold;
        }

        /* Jaga agar warna status (success, warning, info, danger) tetap default */
        .bg-info {
            background-color: #36b9cc !important;
        }

        .bg-warning {
            background-color: #f6c23e !important;
        }

        .bg-success {
            background-color: #1cc88a !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">

        {{-- 2. NAVBAR (Main Header Container) --}}
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            {{-- Link Profil --}}
                            @if (Auth::user()->isPelajar())
                                <a class="dropdown-item" href="{{ route('pelajar.profile') }}"><i
                                        class="fas fa-user mr-2"></i> Profil</a>
                            @elseif(Auth::user()->isPengajar())
                                <a class="dropdown-item" href="{{ route('pengajar.profile') }}"><i
                                        class="fas fa-user mr-2"></i> Profil</a>
                            @elseif(Auth::user()->isAdmin())
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i
                                        class="fas fa-user-shield mr-2"></i> Dashboard Admin</a>
                            @endif

                            <div class="dropdown-divider"></div>

                            {{-- Logout --}}
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm mt-1" href="{{ route('register') }}">Daftar</a>
                    </li>
                @endauth
            </ul>
        </nav>
        {{-- /.navbar --}}

        {{-- 3. MAIN SIDEBAR CONTAINER --}}
        <aside class="main-sidebar sidebar-light-primary elevation-4">
            <a href="" class="brand-link d-flex align-items-center">
                <i class="fas fa-graduation-cap brand-image img-circle elevation-3"
                    style="opacity: .8; margin-top: -3px;"></i>
                <span class="brand-text font-weight-bold">Pengajar Privat</span>
            </a>

            <div class="sidebar">
                @auth
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                            data-accordion="false">

                            {{-- NAVIGASI BERDASARKAN ROLE (Diambil dari kode Anda sebelumnya) --}}
                            @if (Auth::user()->isAdmin())
                                <li class="nav-header">ADMIN MENU</li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                                {{-- Tambahkan menu Admin lainnya sesuai yang ada di kode lama Anda --}}
                                <li class="nav-item">
                                    <a href="{{ route('admin.pengajar') }}"
                                        class="nav-link {{ request()->routeIs('admin.pengajar') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                                        <p>Kelola Pengajar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.permintaan') }}"
                                        class="nav-link {{ request()->routeIs('admin.permintaan') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-envelope"></i>
                                        <p>Kelola Permintaan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.ulasan') }}"
                                        class="nav-link {{ request()->routeIs('admin.ulasan') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-comments"></i>
                                        <p>Kelola Ulasan</p>
                                    </a>
                                </li>
                            @elseif(Auth::user()->isPengajar())
                                <li class="nav-header">PENGAJAR MENU</li>
                                <li class="nav-item">
                                    <a href="{{ route('pengajar.dashboard') }}"
                                        class="nav-link {{ request()->routeIs('pengajar.dashboard') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pengajar.profile') }}"
                                        class="nav-link {{ request()->routeIs('pengajar.profile') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-user"></i>
                                        <p>Profil Saya</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pengajar.permintaan') }}"
                                        class="nav-link {{ request()->routeIs('pengajar.permintaan') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-envelope"></i>
                                        <p>Permintaan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pengajar.ulasan') }}"
                                        class="nav-link {{ request()->routeIs('pengajar.ulasan') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-star"></i>
                                        <p>Ulasan</p>
                                    </a>
                                </li>
                            @elseif(Auth::user()->isPelajar())
                                <li class="nav-header">PELAJAR MENU</li>
                                <li class="nav-item">
                                    <a href="{{ route('pelajar.dashboard') }}"
                                        class="nav-link {{ request()->routeIs('pelajar.dashboard') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pelajar.search.form') }}"
                                        class="nav-link {{ request()->routeIs('pelajar.search.form') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-search"></i>
                                        <p>Cari Pengajar</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('pelajar.permintaan.index') }}"
                                        class="nav-link {{ request()->routeIs('pelajar.permintaan.index') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-history"></i>
                                        <p>Riwayat Permintaan</p>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </nav>
                @endauth
            </div>
        </aside>

        {{-- 4. CONTENT WRAPPER. Contains page content --}}
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                    @endif
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>
        {{-- /.content-wrapper --}}

        {{-- 5. MAIN FOOTER --}}
        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                Version 1.0 (KNN RPL)
            </div>
            <strong>&copy; 2025 Sistem Pengajar Privat Majene.</strong> Universitas Sulawesi Barat.
        </footer>
    </div>
    {{-- 6. REQUIRED SCRIPTS (Ganti Bootstrap 5 CDN dengan AdminLTE/Bootstrap 4 local assets) --}}
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


    @stack('scripts')
</body>

</html>
