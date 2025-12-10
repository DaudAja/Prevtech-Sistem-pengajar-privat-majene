<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengajar Privat')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @stack('styles')

    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fc;
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }

        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: bold;
            color: var(--primary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }

        .stat-card {
            border-left: 4px solid;
        }

        .stat-card.primary {
            border-left-color: var(--primary-color);
        }

        .stat-card.success {
            border-left-color: var(--success-color);
        }

        .stat-card.warning {
            border-left-color: var(--warning-color);
        }

        .stat-card.danger {
            border-left-color: var(--danger-color);
        }

        .badge-status {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .rating-stars {
            color: #f6c23e;
        }

        .map-container {
            height: 400px;
            border-radius: 0.35rem;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .alert {
            border-radius: 0.35rem;
            border: none;
        }

        footer {
            background-color: #fff;
            padding: 1.5rem 0;
            margin-top: 3rem;
            border-top: 1px solid #e3e6f0;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap"></i> Pengajar Privat Majene
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if (Auth::user()->isPelajar())
                                    <li><a class="dropdown-item" href="{{ route('pelajar.profile') }}"><i
                                                class="fas fa-user"></i> Profil</a></li>
                                @elseif(Auth::user()->isPengajar())
                                    <li><a class="dropdown-item" href="{{ route('pengajar.profile') }}"><i
                                                class="fas fa-user"></i> Profil</a></li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Daftar</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            @if (Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                        href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.pengajar') ? 'active' : '' }}"
                                        href="{{ route('admin.pengajar') }}">
                                        <i class="fas fa-chalkboard-teacher"></i> Kelola Pengajar
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.pelajar') ? 'active' : '' }}"
                                        href="{{ route('admin.pelajar') }}">
                                        <i class="fas fa-user-graduate"></i> Kelola Pelajar
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.rekomendasi') ? 'active' : '' }}"
                                        href="{{ route('admin.rekomendasi') }}">
                                        <i class="fas fa-star"></i> Kelola Rekomendasi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.permintaan') ? 'active' : '' }}"
                                        href="{{ route('admin.permintaan') }}">
                                        <i class="fas fa-envelope"></i> Kelola Permintaan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.ulasan') ? 'active' : '' }}"
                                        href="{{ route('admin.ulasan') }}">
                                        <i class="fas fa-comments"></i> Kelola Ulasan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}"
                                        href="{{ route('admin.reports') }}">
                                        <i class="fas fa-chart-bar"></i> Laporan
                                    </a>
                                </li>
                            @elseif(Auth::user()->isPengajar())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pengajar.dashboard') ? 'active' : '' }}"
                                        href="{{ route('pengajar.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pengajar.profile') ? 'active' : '' }}"
                                        href="{{ route('pengajar.profil') }}">
                                        <i class="fas fa-user"></i> Profil Saya
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pengajar.permintaan') ? 'active' : '' }}"
                                        href="{{ route('pengajar.permintaan') }}">
                                        <i class="fas fa-envelope"></i> Permintaan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pengajar.jadwal') ? 'active' : '' }}"
                                        href="{{ route('pengajar.jadwal') }}">
                                        <i class="fas fa-calendar"></i> Jadwal
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pengajar.ulasan') ? 'active' : '' }}"
                                        href="{{ route('pengajar.ulasan') }}">
                                        <i class="fas fa-star"></i> Ulasan
                                    </a>
                                </li>
                            @elseif(Auth::user()->isPelajar())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pelajar.dashboard') ? 'active' : '' }}"
                                        href="{{ route('pelajar.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                </li>

                                <li class="nav-item">
                                    {{-- ROUTE SESUAI: pelajar.search.form --}}
                                    <a class="nav-link {{ request()->routeIs('pelajar.search.form') ? 'active' : '' }}"
                                        href="{{ route('pelajar.search.form') }}">
                                        <i class="fas fa-search"></i> Cari Pengajar
                                    </a>
                                </li>

                                {{-- <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('pelajar.profile') ? 'active' : '' }}"
                                        href="{{ route('pelajar.profile') }}">
                                        <i class="fas fa-user"></i> Profil Saya
                                    </a>
                                </li> --}}
                            @endif
                        </ul>
                    </div>
                </nav>
            @endauth

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="py-4">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center text-muted">
        <div class="container">
            <p class="mb-0">&copy; 2025 Sistem Pengajar Privat Majene. Universitas Sulawesi Barat.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>

</html>
