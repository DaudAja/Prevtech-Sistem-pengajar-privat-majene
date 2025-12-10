<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Pengajar Privat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .register-card {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        #pengajarFields {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card register-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold">Registrasi</h3>
                            <p class="text-muted">Buat akun baru Anda</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('register') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" required>
                                    <small class="text-muted">Minimal 8 karakter</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Daftar Sebagai <span class="text-danger">*</span></label>
                                    <select name="role" id="roleSelect" class="form-select" required>
                                        <option value="">Pilih Role</option>
                                        <option value="pelajar" {{ old('role') == 'pelajar' ? 'selected' : '' }}>Pelajar</option>
                                        <option value="pengajar" {{ old('role') == 'pengajar' ? 'selected' : '' }}>Pengajar</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Umur</label>
                                    <input type="number" name="umur" class="form-control" value="{{ old('umur') }}" min="10" max="100">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon') }}" required>
                            </div>

                            <!-- Pengajar-specific fields -->
                            <div id="pengajarFields">
                                <hr class="my-4">
                                <h5 class="mb-3 text-primary">Informasi Pengajar</h5>

                                <div class="mb-3">
                                    <label class="form-label">Mata Pelajaran yang Diajarkan</label>
                                    <input type="text" name="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran') }}" placeholder="Contoh: Matematika, Fisika">
                                    <small class="text-muted">Pisahkan dengan koma jika lebih dari satu</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pendidikan Terakhir</label>
                                        <input type="text" name="pendidikan_terakhir" class="form-control" value="{{ old('pendidikan_terakhir') }}" placeholder="Contoh: S1 Pendidikan Matematika">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pengalaman Mengajar (Tahun)</label>
                                        <input type="number" name="pengalaman_tahun" class="form-control" value="{{ old('pengalaman_tahun', 0) }}" min="0">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Singkat</label>
                                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Ceritakan tentang metode mengajar dan keahlian Anda">{{ old('deskripsi') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tarif Per Jam (Rp)</label>
                                    <input type="number" name="tarif_per_jam" class="form-control" value="{{ old('tarif_per_jam', 0) }}" min="0" step="1000">
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-0">Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Login di sini</a></p>
                            <p class="mt-2"><a href="/" class="text-decoration-none"><i class="fas fa-home"></i> Kembali ke Beranda</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide pengajar fields based on role selection
        document.getElementById('roleSelect').addEventListener('change', function() {
            const pengajarFields = document.getElementById('pengajarFields');
            if (this.value === 'pengajar') {
                pengajarFields.style.display = 'block';
            } else {
                pengajarFields.style.display = 'none';
            }
        });

        // Check on page load (for old input)
        window.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('roleSelect');
            if (roleSelect.value === 'pengajar') {
                document.getElementById('pengajarFields').style.display = 'block';
            }
        });
    </script>
</body>
</html>
