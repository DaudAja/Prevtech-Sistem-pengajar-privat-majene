@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">Cari & Dapatkan Rekomendasi</h1>

    <div class="bg-white p-4 rounded shadow">
        <form action="{{ route('pelajar.search.results') }}" method="POST" id="searchForm">
            @csrf

            {{-- Bagian Input Kriteria --}}
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mata Pelajaran</label>
                    <input name="mata_pelajaran" class="form-control" placeholder="Contoh: Matematika">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jarak Maksimal (Km)</label>
                    {{-- Anda bisa menambahkan filter jarak di sini, default 50km di KNNService --}}
                    <input type="number" name="radius" class="form-control" value="10" min="1" max="100" placeholder="Contoh: 10">
                </div>
            </div>

            <hr>

            {{-- Bagian Lokasi Interaktif --}}
            <h5 class="mb-3">Tentukan Lokasi Pencarian (Pilih Salah Satu)</h5>

            <div class="alert alert-info py-2" role="alert">
                <i class="fas fa-info-circle"></i> Klik tombol di bawah atau **tandai lokasi di peta** untuk mendapatkan koordinat otomatis.
            </div>

            <div class="d-flex gap-2 mb-3">
                <button type="button" id="geoBtn" class="btn btn-sm btn-primary">
                    <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya (GPS)
                </button>
            </div>

            {{-- Wadah Peta Interaktif --}}
            <div id="locationMap" style="height: 350px; border-radius: 8px;"></div>

            {{-- Input Koordinat (Disembunyikan, diisi otomatis oleh JS) --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label text-sm">Latitude</label>
                    <input name="latitude" id="lat" class="form-control form-control-sm" required readonly placeholder="Diisi Otomatis/Map Klik">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-sm">Longitude</label>
                    <input name="longitude" id="lon" class="form-control form-control-sm" required readonly placeholder="Diisi Otomatis/Map Klik">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-lg btn-success w-100">
                    <i class="fas fa-search"></i> Cari Pengajar Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const defaultLat = -3.5410; // Majene default/contoh
    const defaultLng = 118.9710;
    const latInput = document.getElementById('lat');
    const lonInput = document.getElementById('lon');
    let searchMarker; // Marker untuk lokasi pencarian

    // 1. Inisialisasi Peta Leaflet
    if (typeof L === 'undefined') {
        console.error("Leaflet.js tidak dimuat. Cek resources/views/layouts/app.blade.php");
        document.getElementById('locationMap').innerHTML = '<p class="text-center p-5">Gagal memuat peta. Silakan coba metode input manual (Lat/Lng).</p>';
        return;
    }

    var map = L.map('locationMap').setView([defaultLat, defaultLng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // 2. Fungsi untuk Menetapkan Lokasi
    function setLocation(lat, lng) {
        latInput.value = lat.toFixed(6);
        lonInput.value = lng.toFixed(6);

        // Hapus marker lama
        if (searchMarker) {
            map.removeLayer(searchMarker);
        }

        // Tambahkan marker baru
        searchMarker = L.marker([lat, lng]).addTo(map)
            .bindPopup("Lokasi Pencarian Anda")
            .openPopup();

        map.setView([lat, lng], 14); // Pindahkan tampilan peta
    }

    // 3. Auto-load Lokasi Saat Halaman Dimuat (UX Improvement)
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            setLocation(position.coords.latitude, position.coords.longitude);
        }, function(error) {
            console.warn('Geolocation ditolak atau error: ' + error.message);
            // Fallback ke lokasi default Majene jika gagal
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

        // Hapus nilai input manual (jika ada)
        latInput.value = '';
        lonInput.value = '';

        navigator.geolocation.getCurrentPosition(function(position) {
            setLocation(position.coords.latitude, position.coords.longitude);
        }, function(err) {
            alert('Gagal mendapatkan lokasi GPS: ' + err.message);
        });
    });

    // 5. Handle Klik pada Peta (UX Improvement)
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
