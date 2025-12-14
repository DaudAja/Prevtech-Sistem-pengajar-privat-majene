<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi - Sistem Pengajar Privat</title>

    {{-- Menggunakan aset AdminLTE/Bootstrap 4 --}}
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    <style>
        /* Definisi Warna Maroon Kustom */
        .text-maroon { color: #800000 !important; }
        .bg-maroon-custom { background-color: #800000 !important; }

        /* Override AdminLTE Body untuk menggunakan background gradient */
        .register-page {
            /* Warna latar belakang sama dengan halaman login */
            background: linear-gradient(135deg, #800000 0%, #A52A2A 100%) !important;
            padding-top: 50px;
            padding-bottom: 50px;
        }

        /* Mengubah lebar register-box menjadi lebih kecil (500px) */
        .register-box {
            width: 500px;
            max-width: 90%;
        }

        /* Mengubah warna border card utama menjadi maroon */
        .card-primary.card-outline {
            border-top: 3px solid #800000 !important;
        }
        /* Style untuk bagian Pengajar (Untuk membedakan) */
        .card-pengajar {
            border: 1px solid #ced4da;
            padding: 15px;
            margin-top: 20px;
            border-radius: .25rem;
        }
        /* Memastikan baris tidak memiliki margin-left/right di Auth Pages */
        .register-card-body .row {
            margin-left: 0;
            margin-right: 0;
        }
    </style>
</head>
<body class="hold-transition register-page">

    <div class="register-box"> {{-- Lebar 500px --}}
        <div class="register-logo">
            {{-- Menggunakan warna putih agar terlihat di background maroon --}}
            <a href="/" class="text-white"><b>Sistem</b> Privat</a>
        </div>

        <div class="card card-outline card-primary">
            <div class="card-body register-card-body">
                <p class="login-box-msg">Daftar Akun Baru</p>

                {{-- Tampilan Error Laravel (Alert AdminLTE) --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    {{-- BAGIAN 1: DATA AKUN DASAR --}}
                    <h5 class="mb-3 text-maroon font-weight-bold">Data Akun</h5>

                    {{-- Nama Lengkap --}}
                    <div class="input-group mb-3">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap *" value="{{ old('name') }}" required>
                        <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
                    </div>

                    {{-- Email --}}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email *" value="{{ old('email') }}" required>
                        <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
                    </div>

                    {{-- Password (2 Kolom) --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password *" required>
                                <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi Password *" required>
                                <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
                            </div>
                        </div>
                    </div>

                    {{-- BAGIAN 2: DATA DIRI & ROLE --}}
                    <h5 class="mb-3 mt-4 text-maroon font-weight-bold">Data Diri</h5>

                    {{-- Role dan Umur (2 Kolom) --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Daftar Sebagai <span class="text-danger">*</span></label>
                                <select name="role" id="roleSelect" class="form-control @error('role') is-invalid @enderror" required>
                                    <option value="">Pilih Role</option>
                                    <option value="pelajar" {{ old('role') == 'pelajar' ? 'selected' : '' }}>Pelajar</option>
                                    <option value="pengajar" {{ old('role') == 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Umur</label>
                                <input type="number" name="umur" class="form-control @error('umur') is-invalid @enderror" value="{{ old('umur') }}" min="10" max="100" placeholder="Umur (Opsional)">
                            </div>
                        </div> --}}
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div class="form-group mb-3">
                        <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2" required>{{ old('alamat') }}</textarea>
                    </div>

                    {{-- No. Telepon --}}
                    <div class="input-group mb-3">
                        <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon') }}" placeholder="No. Telepon *" required>
                        <div class="input-group-append"><div class="input-group-text"><span class="fas fa-phone"></span></div></div>
                    </div>


                    {{-- BAGIAN 3: INFORMASI PENGAJAR (KONDISIONAL) --}}
                    <div id="pengajarFields" class="card-pengajar" style="display: {{ old('role') == 'pengajar' ? 'block' : 'none' }};">
                        <h5 class="mb-3 text-maroon font-weight-bold"><i class="fas fa-chalkboard-teacher"></i> Detail Pengajar</h5>

                        {{-- Mata Pelajaran --}}
                        <div class="form-group mb-3">
                            <label class="form-label">Mata Pelajaran yang Diajarkan</label>
                            <input type="text" name="mata_pelajaran" class="form-control @error('mata_pelajaran') is-invalid @enderror" value="{{ old('mata_pelajaran') }}" placeholder="Contoh: Matematika, Fisika, Kimia">
                            <small class="text-muted">Pisahkan dengan koma jika lebih dari satu</small>
                        </div>

                        {{-- Pendidikan Terakhir --}}
                        {{-- <div class="form-group mb-3">
                            <label class="form-label">Pendidikan Terakhir</label>
                            <input type="text" name="pendidikan_terakhir" class="form-control @error('pendidikan_terakhir') is-invalid @enderror" value="{{ old('pendidikan_terakhir') }}" placeholder="Contoh: S1 Pendidikan Matematika">
                        </div> --}}

                        {{-- Pengalaman Mengajar --}}
                        {{-- <div class="form-group mb-3">
                            <label class="form-label">Pengalaman Mengajar (Tahun)</label>
                            <input type="number" name="pengalaman_tahun" class="form-control @error('pengalaman_tahun') is-invalid @enderror" value="{{ old('pengalaman_tahun', 0) }}" min="0">
                        </div> --}}

                        {{-- Tarif Per Jam --}}
                        {{-- <div class="form-group mb-3">
                            <label class="form-label">Tarif Per Jam (Rp)</label>
                            <input type="number" name="tarif_per_jam" class="form-control @error('tarif_per_jam') is-invalid @enderror" value="{{ old('tarif_per_jam', 0) }}" min="0" step="1000" placeholder="Contoh: 50000">
                        </div> --}}

                        {{-- Deskripsi Singkat --}}
                        <div class="form-group mb-0">
                            <label class="form-label">Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="2" placeholder="Ceritakan tentang metode mengajar dan keahlian Anda">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>


                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-block bg-maroon-custom btn-lg text-white">
                                <i class="fas fa-user-plus"></i> Daftar Sekarang
                            </button>
                        </div>
                    </div>
                </form>

                <p class="mt-3 mb-1 text-center">
                    <a href="{{ route('login') }}" class="text-maroon">Sudah punya akun? Login di sini</a>
                </p>
                <p class="mb-0 text-center">
                    <a href="{{ url('/') }}" class="text-maroon"><i class="fas fa-home"></i> Kembali ke Beranda</a>
                </p>
            </div> {{-- /.card-body --}}
        </div> {{-- /.card --}}
    </div> {{-- /.register-box --}}

    {{-- Aset JavaScript AdminLTE/Bootstrap 4 --}}
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

    <script>
        // Logic untuk menampilkan/menyembunyikan field pengajar
        document.getElementById('roleSelect').addEventListener('change', function() {
            const pengajarFields = document.getElementById('pengajarFields');
            if (this.value === 'pengajar') {
                pengajarFields.style.display = 'block';
            } else {
                pengajarFields.style.display = 'none';
            }
        });
    </script>
</body>
</html>
