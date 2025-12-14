@extends('layouts.app')

@section('content')

{{-- AdminLTE Content Header --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0 text-dark"><i class="fas fa-search"></i> Cari & Dapatkan Rekomendasi</h1>
            </div>
        </div>
    </div>
</div>

{{-- Main Content Section --}}
<section class="content">
    <div class="container-fluid">
        {{-- Pembungkus Card AdminLTE --}}
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Kriteria Pencarian Pengajar</h3>
            </div>

            <form action="{{ route('pelajar.search.results') }}" method="POST" id="searchForm">
                @csrf
                <div class="card-body">

                    {{-- Bagian Input Kriteria --}}
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label">Mata Pelajaran</label>
                            <input name="mata_pelajaran" class="form-control" placeholder="Contoh: Matematika">
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">Pengalaman  (Tahun)</label>
                            <input name="pengalaman_tahun" class="form-control" placeholder="Contoh: 10">
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">Jarak Maksimal (Km)</label>
                            <input type="number" name="radius" class="form-control" value="10" min="1" max="100" placeholder="Contoh: 10">
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 text-bold">Tentukan Lokasi Pencarian (Pilih Salah Satu)</h5>

                    <div class="alert alert-info alert-dismissible">
                        <i class="fas fa-info-circle"></i> Klik tombol di bawah atau **tandai lokasi di peta** untuk mendapatkan koordinat otomatis.
                    </div>

                    {{-- Kontrol Lokasi --}}
                    <div class="d-flex mb-3">
                        <button type="button" id="geoBtn" class="btn btn-sm btn-primary">
                            <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya (GPS)
                        </button>
                    </div>

                    {{-- Wadah Peta Interaktif --}}
                    {{-- ID PETA DI SINI HARUS 'locationMap' --}}
                    <div id="locationMap" style="height: 350px; border-radius: 4px;" class="mb-3"></div>

                    {{-- Input Koordinat --}}
                    <div class="row mt-3">
                        <div class="col-md-6 form-group">
                            <label class="form-label text-sm">Latitude</label>
                            <input name="latitude" id="lat" class="form-control form-control-sm" required readonly placeholder="Diisi Otomatis/Map Klik">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label text-sm">Longitude</label>
                            <input name="longitude" id="lon" class="form-control form-control-sm" required readonly placeholder="Diisi Otomatis/Map Klik">
                        </div>
                    </div>
                </div> {{-- /.card-body --}}

                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-lg btn-block">
                        <i class="fas fa-search"></i> Cari Pengajar Sekarang
                    </button>
                </div> {{-- /.card-footer --}}
            </form>
        </div> {{-- /.card --}}
    </div> {{-- /.container-fluid --}}
</section>
@endsection

@push('styles')
<style>
/* FIX KRITIS LEAFLET LOKAL: Memastikan tile tidak terdistorsi di dalam AdminLTE */
.leaflet-container img {
    max-width: none !important;
    display: inline !important;
    border: none !important;
    box-shadow: none !important;
}
.leaflet-container {
    z-index: 1;
    overflow: hidden;
}
#locationMap { /* Target ID peta di halaman ini */
    overflow: hidden !important;
}
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const defaultLat = -3.5410;
    const defaultLng = 118.9710;
    const latInput = document.getElementById('lat');
    const lonInput = document.getElementById('lon');
    let searchMarker;
    let map;

    // 1. Inisialisasi Peta Leaflet
    if (typeof L === 'undefined') {
        document.getElementById('locationMap').innerHTML = '<p class="text-center p-5">Gagal memuat peta. Cek konsol browser (F12).</p>';
        return;
    }

    try {
        // ID peta di halaman ini adalah 'locationMap'
        map = L.map('locationMap').setView([defaultLat, defaultLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
    } catch (e) {
        document.getElementById('locationMap').innerHTML = '<p class="text-center p-5">Gagal menginisialisasi peta.</p>';
        return;
    }

    /* -------------------------------------------------------------------- */
    /* **A. SOLUSI TIMING AGRESIF (MULTIPLE RESIZE)** */
    /* -------------------------------------------------------------------- */

    // Resize 1: Segera setelah peta dibuat (100ms)
    setTimeout(function () {
        if (map) map.invalidateSize();
    }, 100);

    // Resize 2: Setelah transisi AdminLTE selesai (500ms)
    setTimeout(function () {
        if (map) map.invalidateSize();
    }, 500);

    // Resize 3: Cadangan (1000ms)
    setTimeout(function () {
        if (map) {
             map.invalidateSize();
             map.panTo(new L.LatLng(map.getCenter().lat, map.getCenter().lng));
        }
    }, 1000);


    /* -------------------------------------------------------------------- */
    /* **B. RESIZE PADA PERUBAHAN SIDEBAR ADMINLTE** */
    /* -------------------------------------------------------------------- */
    // Memastikan peta diukur ulang setiap kali sidebar dibuka/ditutup
    $(document).on('expanded.lte.pushmenu collapsed.lte.pushmenu', function () {
        if (map) {
            map.invalidateSize();
            map.panTo(new L.LatLng(map.getCenter().lat, map.getCenter().lng));
        }
    });


    // 2. Fungsi untuk Menetapkan Lokasi
    function setLocation(lat, lng) {
        latInput.value = lat.toFixed(6);
        lonInput.value = lng.toFixed(6);

        if (searchMarker) {
            map.removeLayer(searchMarker);
        }

        searchMarker = L.marker([lat, lng]).addTo(map)
            .bindPopup("Lokasi Pencarian Anda")
            .openPopup();

        map.setView([lat, lng], 14);
    }

    // 3. Auto-load Lokasi Saat Halaman Dimuat
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            setLocation(position.coords.latitude, position.coords.longitude);
        }, function(error) {
            setLocation(defaultLat, defaultLng);
        });
    } else {
        setLocation(defaultLat, defaultLng);
    }


    // 4. Handle Tombol "Gunakan Lokasi Saya"
    document.getElementById('geoBtn').addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocation tidak didukung di browser ini.');
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            setLocation(position.coords.latitude, position.coords.longitude);
        }, function(err) {
            alert('Gagal mendapatkan lokasi GPS: ' + err.message);
        });
    });

    // 5. Handle Klik pada Peta
    map.on('click', function(e) {
        setLocation(e.latlng.lat, e.latlng.lng);
        Swal.fire({
            icon: 'success',
            title: 'Lokasi Dipilih!',
            text: `Koordinat (Lat/Lng) berhasil diisi dari peta.`,
            showConfirmButton: false,
            timer: 1500
        });
    });

});
</script>
@endpush
