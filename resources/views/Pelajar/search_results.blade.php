@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <h1 class="text-2xl font-bold mb-4">Hasil Rekomendasi Pengajar</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- WADAH PETA --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-white">
            <h5 class="m-0 font-weight-bold text-primary">Visualisasi Lokasi Pengajar Terdekat</h5>
        </div>
        <div class="card-body">
            <div id="mapid" style="height: 400px; border-radius: 8px;"></div>
        </div>
    </div>
    {{-- END WADAH PETA --}}

    <div class="card shadow p-4">
        <h2 class="h5 mb-3 text-gray-800">Daftar Pengajar yang Direkomendasikan</h2>

        <div class="mb-4 text-sm text-gray-600">
            Pencarian untuk: <strong>{{ $criteria['mata_pelajaran'] ?? 'Semua mapel' }}</strong>
            (Lokasi Anda: {{ round($criteria['latitude'], 6) }}, {{ round($criteria['longitude'], 6) }})
        </div>

        @if(empty($results))
            <p class="text-sm text-gray-600">Tidak ada pengajar yang ditemukan dalam kriteria yang ditentukan.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Peringkat</th>
                            <th>Nama Pengajar</th>
                            <th>Mata Pelajaran</th>
                            <th>Jarak (KM)</th>
                            <th>Skor Kemiripan (KNN)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $r)
                            @php $p = $r['pengajar']; @endphp
                            <tr class="align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ optional($p->user)->name ?? 'N/A' }}
                                    @if($p->status_verifikasi)
                                        <span class="badge bg-success text-white" title="Terverifikasi"><i class="fas fa-check"></i> Verified</span>
                                    @endif
                                </td>
                                <td>{{ $p->mata_pelajaran }}</td>
                                <td>
                                    <strong>{{ $r['distance'] }}</strong>
                                    <small class="text-muted">km</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary text-white">{{ $r['score'] }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('pelajar.pengajar.show', $p->id) }}" class="btn btn-sm btn-info me-2">Detail</a>
                                    {{-- Menggunakan Modal untuk action request --}}
                                    <a href="#" class="btn btn-sm btn-success btn-request" data-bs-toggle="modal" data-bs-target="#requestModal" data-pengajar-id="{{ $p->id }}" data-pengajar-name="{{ optional($p->user)->name }}">Minta Privat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Modal Permintaan Privat (KOREKSI USE CASE: Mengganti "Menghubungi Pelajar" menjadi "Mengirim Permintaan") --}}
<div class="modal fade" id="requestModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Minta Privat dengan <span id="tutorName"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="requestForm" method="POST">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="jadwal_diinginkan" class="form-label">Jadwal Diinginkan</label>
                <input type="datetime-local" class="form-control" id="jadwal_diinginkan" name="jadwal_diinginkan" required>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan Tambahan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Saya butuh fokus pada bab trigonometri."></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- End Modal --}}


@push('scripts')
{{-- BLOK KRITIS YANG DIPERBAIKI --}}
@php
    // Memproses data Collection PHP agar aman dienkode ke JSON
    $safeTutorData = collect($results)
        ->map(function($r) {
            $user = optional($r['pengajar'])->user; // Ambil relasi user sekali

            // Periksa apakah user dan koordinat tersedia
            if (!optional($user)->latitude || !optional($user)->longitude) {
                return null;
            }

            return [
                'lat' => $user->latitude,
                'lng' => $user->longitude,
                'name' => optional($user)->name,
                'distance' => $r['distance'],
                'mapel' => $r['pengajar']->mata_pelajaran
            ];
        })
        ->filter() // Hapus entri null (pengajar tanpa lokasi)
        ->values()
        ->all();
@endphp
{{-- END BLOK KRITIS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // 1. Ambil Data Lokasi dari Blade ke JavaScript (menggunakan variabel PHP yang sudah diproses)
    const userLat = {{ $criteria['latitude'] }};
    const userLng = {{ $criteria['longitude'] }};
    const tutorData = @json($safeTutorData); // <-- Menggunakan variabel PHP yang aman

    // Cek apakah Leaflet sudah dimuat dan apakah ada data tutor untuk ditampilkan
    if (typeof L !== 'undefined' && tutorData.length > 0) {
        // 2. Inisialisasi Peta Leaflet
        // Peta akan terpusat di lokasi pengguna
        var map = L.map('mapid').setView([userLat, userLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var markersGroup = new L.FeatureGroup();

        // Icon kustom untuk Pelajar (Warna Biru)
        var studentIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<i class="fas fa-user-graduate fa-2x" style="color: #4e73df;"></i>',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        // Icon kustom untuk Pengajar (Warna Merah)
        var tutorIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<i class="fas fa-chalkboard-teacher fa-2x" style="color: #e74a3b;"></i>',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });


        // 3. Tambahkan Marker Pelajar (User)
        var userMarker = L.marker([userLat, userLng], {icon: studentIcon})
            .bindPopup("<b>Lokasi Anda (Pencarian)</b>")
            .openPopup();
        markersGroup.addLayer(userMarker);

        // 4. Tambahkan Marker Pengajar (Tutor)
        tutorData.forEach(function(tutor) {
            var popupContent = `
                <b>${tutor.name}</b><br>
                Mapel: ${tutor.mapel}<br>
                Jarak: ${tutor.distance} km
            `;
            var tutorMarker = L.marker([tutor.lat, tutor.lng], {icon: tutorIcon})
                .bindPopup(popupContent);
            markersGroup.addLayer(tutorMarker);
        });

        // 5. Sesuaikan tampilan peta agar semua marker terlihat
        map.addLayer(markersGroup);
        if (markersGroup.getBounds().isValid()) {
             map.fitBounds(markersGroup.getBounds().pad(0.5)); // Padding 0.5 agar tidak terlalu mepet
        }
    } else {
        // Jika Leaflet belum dimuat (walaupun sudah di include di layout) atau data kurang
        document.getElementById('mapid').innerHTML = '<p class="text-center text-muted p-5">Map tidak dapat ditampilkan. Pastikan koneksi internet stabil dan data lokasi pengajar valid.</p>';
    }

    // LOGIC UNTUK MODAL PERMINTAAN
    // Saat tombol "Minta Privat" diklik
    document.querySelectorAll('.btn-request').forEach(function(button) {
        button.addEventListener('click', function() {
            const pengajarId = this.getAttribute('data-pengajar-id');
            const pengajarName = this.getAttribute('data-pengajar-name');

            // Atur nama pengajar di modal
            document.getElementById('tutorName').textContent = pengajarName;
            // Atur action URL untuk form request
            document.getElementById('requestForm').action = `/pelajar/requests/${pengajarId}/create`;
        });
    });
});
</script>
@endpush
