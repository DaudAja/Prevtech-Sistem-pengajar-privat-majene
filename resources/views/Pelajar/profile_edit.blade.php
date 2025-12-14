@extends('layouts.app')

@section('content')

{{-- 1. Content Header AdminLTE --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0 text-dark"><i class="fas fa-edit"></i> Edit Profil Pelajar</h1>
            </div>
        </div>
    </div>
</div>

{{-- 2. Main Content Section --}}
<section class="content">
    <div class="container-fluid">

        {{-- Flash Messages (Jika ada) --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Kartu Formulir Edit Profil --}}
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Informasi Akun</h3>
            </div>

            <form action="{{ route('pelajar.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">

                    {{-- Nama --}}
                    <div class="form-group">
                        <label for="name">Nama <span class="text-danger">*</span></label>
                        <input name="name" id="name" type="text" value="{{ old('name', $user->name) }}" class="form-control" required>
                    </div>

                    {{-- No Telepon --}}
                    <div class="form-group">
                        <label for="no_telepon">No Telepon</label>
                        <input name="no_telepon" id="no_telepon" type="text" value="{{ old('no_telepon', $user->no_telepon) }}" class="form-control">
                    </div>

                    {{-- Latitude & Longitude (Row untuk grid) --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input name="latitude" id="latitude" type="text" value="{{ old('latitude', $user->latitude) }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input name="longitude" id="longitude" type="text" value="{{ old('longitude', $user->longitude) }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="button" id="geoBtn" class="btn btn-primary btn-sm mb-3">
                            <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya
                        </button>
                    </div>

                    {{-- Foto Profil --}}
                    {{-- <div class="form-group">
                        <label for="foto_profil">Foto Profil (opsional)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="foto_profil" class="custom-file-input" id="foto_profil" accept="image/*">
                                <label class="custom-file-label" for="foto_profil">Pilih file...</label>
                            </div>
                        </div> --}}

                        {{-- Tampilan Foto yang Sudah Ada --}}
                        {{-- @if($user->foto_profil)
                            <div class="mt-3">
                                <label>Foto Saat Ini:</label>
                                <img src="{{ asset('storage/'.$user->foto_profil) }}"
                                     alt="foto profil"
                                     class="img-fluid img-circle elevation-2"
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                        @endif
                    </div> --}}

                </div> {{-- /.card-body --}}

                <div class="card-footer d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div> {{-- /.card-footer --}}
            </form>
        </div> {{-- /.card --}}

    </div> {{-- /.container-fluid --}}
</section>
@endsection

@push('scripts')
<script>
// Skrip Geolocation
document.getElementById('geoBtn').addEventListener('click', function(){
    if (!navigator.geolocation) {
        alert('Geolocation tidak didukung di browser ini.');
        return;
    }

    // Tampilkan loading/indikator jika perlu

    navigator.geolocation.getCurrentPosition(function(position){
        // Sukses mendapatkan lokasi
        document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
        document.getElementById('longitude').value = position.coords.longitude.toFixed(6);

        // Tampilkan notifikasi sukses AdminLTE
        Swal.fire({
            icon: 'success',
            title: 'Lokasi Diperoleh!',
            text: 'Koordinat Latitude dan Longitude telah diisi.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

    }, function(err){
        // Gagal mendapatkan lokasi
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal mendapatkan lokasi: ' + err.message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    });
});

// Update label custom file input Bootstrap
document.getElementById('foto_profil').addEventListener('change',function(e){
    var fileName = document.getElementById("foto_profil").files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});
</script>
@endpush
