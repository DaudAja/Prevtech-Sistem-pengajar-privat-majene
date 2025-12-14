<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cari Pengajar')</title>

    {{-- ASET MINIMAL --}}
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @stack('styles')

    <style>
    /* ---------------------------------------------------- */
    /* MAROON THEME VARIABLES */
    /* ---------------------------------------------------- */
    :root {
        --maroon: #800000;
        --maroon-dark: #660000;
        --sidebar-width: 250px;
    }

    /* FIX KRITIS LEAFLET TILE HILANG */
    .leaflet-container img { max-width: none !important; display: inline !important; }

    /* ---------------------------------------------------- */
    /* CUSTOM SIDEBAR CSS (NON-ADMINLTE PUSHMENU) */
    /* ---------------------------------------------------- */
    .custom-sidebar {
        width: var(--sidebar-width);
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        background-color: #fff; /* Sidebar Putih */
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        z-index: 1030;
        padding-top: 60px; /* Jarak dari Navbar */
    }

    .sidebar-menu-nav {
        padding: 10px 0;
    }

    .sidebar-menu-nav .nav-item .nav-link {
        display: block;
        padding: 10px 20px;
        color: #333;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .sidebar-menu-nav .nav-link.active {
        background-color: var(--maroon);
        color: #fff !important;
    }

    .sidebar-menu-nav .nav-link:hover {
        background-color: #f0f0f0;
        color: var(--maroon);
    }

    .main-content-wrapper {
        margin-left: var(--sidebar-width);
        min-height: 100vh;
        padding-top: 60px; /* Jarak dari Navbar */
    }

    /* ---------------------------------------------------- */
    /* OVERRIDE MAROON COLOR */
    /* ---------------------------------------------------- */
    .bg-primary, .btn-primary, .card-primary > .card-header { background-color: var(--maroon) !important; }
    .btn-primary:hover { background-color: var(--maroon-dark) !important; }
    .text-primary, .navbar-brand { color: var(--maroon) !important; }
    </style>
</head>

<body>
<div class="wrapper">

    {{-- 1. NAVBAR (Header) --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
        <div class="container-fluid">
            {{-- Brand Logo Custom --}}
            <a href="{{ url('/') }}" class="navbar-brand">
                 <i class="fas fa-graduation-cap brand-image img-circle elevation-3"
                    style="opacity: .8; margin-top: -3px;"></i>
                <span class="brand-text font-weight-bold">Pengajar Privat</span>
            </a>

            <ul class="navbar-nav ml-auto">
                @auth
                    <li class="nav-item">
                         <a class="nav-link" href="#">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger mt-1">Logout</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>

    {{-- 2. CUSTOM SIDEBAR --}}
    <div class="custom-sidebar">
        <div class="sidebar-menu-nav">
            @auth
                @if (Auth::user()->isPelajar())
                    <h6 class="nav-header text-muted px-3 mt-2">PELAJAR MENU</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('pelajar.dashboard') }}" class="nav-link {{ request()->routeIs('pelajar.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('pelajar.search.form') }}" class="nav-link {{ request()->routeIs('pelajar.search.form') ? 'active' : '' }}">
                                <i class="fas fa-search mr-2"></i> Cari Pengajar
                            </a>
                        </li>
                         <li class="nav-item">
                            <a href="{{ route('pelajar.permintaan.index') }}" class="nav-link {{ request()->routeIs('pelajar.permintaan.index') ? 'active' : '' }}">
                                <i class="fas fa-history mr-2"></i> Riwayat Permintaan
                            </a>
                        </li>
                    </ul>
                @endif
            @endauth
        </div>
    </div>

    {{-- 3. MAIN CONTENT WRAPPER --}}
    <div class="main-content-wrapper">
        <div class="content-header p-3">
            {{-- Flash Messages dan Errors --}}
            @if (session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif
            {{-- Tambahkan error validation jika perlu --}}
        </div>

        <section class="content p-3">
            <div class="container-fluid">
                @yield('content') {{-- Konten Peta Kita --}}
            </div>
        </section>

    </div>

    {{-- 4. MAIN FOOTER --}}
    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">Version 1.0 (KNN RPL)</div>
        <strong>&copy; 2025 Sistem Pengajar Privat Majene.</strong> Universitas Sulawesi Barat.
    </footer>
</div>
{{-- 5. REQUIRED SCRIPTS --}}
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@stack('scripts')
</body>
</html>
