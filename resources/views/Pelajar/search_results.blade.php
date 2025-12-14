@extends('layouts.app')

@section('content')

{{-- 1. Content Header AdminLTE --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0 text-dark"><i class="fas fa-list-alt text-maroon"></i> Hasil Rekomendasi Pengajar</h1>
            </div>
        </div>
    </div>
</div>

{{-- 2. MAIN CONTENT WRAPPER: Tambahkan section class="content" --}}
<section class="content">
    <div class="container-fluid">
        {{-- Menggunakan class Bootstrap/AdminLTE untuk padding, misalnya mb-4 --}}
        <div class="row mb-4">
            <div class="col-12">

                {{-- WADAH PETA (Card Primary - Maroon) --}}
                <div class="card card-primary card-outline bg-maroon-custom">
                    <div class="card-header border-0">
                        <h3 class="card-title text-white">Visualisasi Lokasi Pengajar Terdekat</h3>
                    </div>
                    <div class="card-body bg-white rounded-bottom">
                        <div id="mapid" style="height: 400px; border-radius: 4px;"></div>
                    </div>
                </div>
                {{-- END WADAH PETA --}}

                {{-- DAFTAR REKOMENDASI --}}
                <div class="card card-outline card-primary">
                    <div class="card-header bg-maroon-custom text-white">
                        <h3 class="card-title">Daftar Pengajar yang Direkomendasikan</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 text-sm text-muted">
                            Pencarian untuk: <strong>{{ $criteria['mata_pelajaran'] ?? 'Semua mapel' }}</strong>
                            (Lokasi Anda: {{ round($criteria['latitude'], 6) }}, {{ round($criteria['longitude'], 6) }})
                        </div>

                        @if(empty($results))
                            <div class="alert alert-warning">Tidak ada pengajar yang ditemukan dalam kriteria yang ditentukan.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Pengajar</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Jarak (KM)</th>
                                            <th>Skor Kemiripan (FSS)</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $index => $r)
                                            @php $p = $r['pengajar']; @endphp
                                            <tr class="align-middle">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <a href="{{ route('pelajar.pengajar.show', $p->id) }}" class="text-maroon font-weight-bold">
                                                        {{ optional($p->user)->name ?? 'N/A' }}
                                                    </a>
                                                    @if($p->status_verifikasi)
                                                        <span class="badge bg-success text-white" title="Terverifikasi"><i class="fas fa-check"></i> Verified</span>
                                                    @endif
                                                </td>
                                                <td>{{ $p->mata_pelajaran }}</td>
                                                <td>
                                                    {{-- Menggunakan distance_km yang sudah dihitung Haversine --}}
                                                    <strong>{{ $r['distance_km'] }}</strong>
                                                    <small class="text-muted">km</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-maroon-custom text-white">{{ $r['score'] }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('pelajar.pengajar.show', $p->id) }}" class="btn btn-sm btn-info me-2">Detail</a>
                                                    <a href="#" class="btn btn-sm btn-success btn-request" data-toggle="modal" data-target="#requestModal" data-pengajar-id="{{ $p->id }}" data-pengajar-name="{{ optional($p->user)->name }}">Minta Privat</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div> {{-- END col-12 --}}
        </div> {{-- END row --}}
    </div> {{-- END container-fluid --}}
</section>
@endsection
{{-- END Main Content --}}

{{-- Modal Permintaan Privat (di luar @section('content') untuk memastikan dimuat) --}}
<div class="modal fade" id="requestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Minta Privat dengan <span id="tutorName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="requestForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="jadwal_diinginkan" class="form-label">Jadwal Diinginkan</label>
                        <input type="datetime-local" class="form-control" id="jadwal_diinginkan" name="jadwal_diinginkan" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan" class="form-label">Keterangan Tambahan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Saya butuh fokus pada bab trigonometri."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
/* Tambahkan tema maroon untuk konsistensi */
.text-maroon { color: #800000 !important; }
.bg-maroon-custom { background-color: #800000 !important; }
.card-primary.card-outline { border-top: 3px solid #800000 !important; }

/* Hapus CSS yang menimpa margin-left di content-wrapper, biarkan AdminLTE yang handle. */
/* .content-wrapper { margin-left: 250px !important; z-index: 1035 !important; } */

/* FIX AdminLTE untuk Peta Leaflet */
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
#mapid {
    overflow: hidden !important;
}
</style>
@endpush


@push('scripts')
{{-- Include Leaflet JS --}}
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

@php
$safeTutorData = collect($results)
    ->map(function($r) {
        $user = optional($r['pengajar'])->user;
        if (!optional($user)->latitude || !optional($user)->longitude || !isset($r['distance_km'])) {
            return null;
        }
        return [
            'lat' => $user->latitude,
            'lng' => $user->longitude,
            'name' => optional($user)->name,
            'distance_km' => $r['distance_km'], // Menggunakan Haversine untuk display
            'mapel' => $r['pengajar']->mata_pelajaran
        ];
    })
    ->filter()
    ->values()
    ->all();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const maroonColor = '#800000';
    const userLat = {{ $criteria['latitude'] ?? 'null' }};
    const userLng = {{ $criteria['longitude'] ?? 'null' }};
    const tutorData = @json($safeTutorData);
    const mapElement = document.getElementById('mapid');

    // 1. Inisialisasi Peta
    if (userLat !== null && userLng !== null && tutorData.length > 0) {

        var map = L.map('mapid').setView([userLat, userLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var markersGroup = new L.FeatureGroup();

        // Custom Icon untuk Pelajar (Maroon)
        var studentIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<i class="fas fa-user-graduate fa-2x" style="color: ${maroonColor};"></i>`,
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
        });

        // Custom Icon untuk Pengajar (Maroon)
        var tutorIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<i class="fas fa-chalkboard-teacher fa-2x" style="color: ${maroonColor};"></i>`,
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
        });


        // Tambahkan Marker Pelajar (User)
        var userMarker = L.marker([userLat, userLng], {icon: studentIcon})
            .bindPopup("<b>Lokasi Pencarian Anda</b>")
            .openPopup();
        markersGroup.addLayer(userMarker);

        // Tambahkan Marker Pengajar (Tutor)
        tutorData.forEach(function(tutor) {
            var popupContent = `
                <b>${tutor.name}</b><br>
                Mapel: ${tutor.mapel}<br>
                Jarak: <strong>${tutor.distance_km} km</strong>
            `;
            var tutorMarker = L.marker([tutor.lat, tutor.lng], {icon: tutorIcon})
                .bindPopup(popupContent);
            markersGroup.addLayer(tutorMarker);
        });

        // Sesuaikan tampilan peta agar semua marker terlihat
        map.addLayer(markersGroup);
        if (markersGroup.getBounds().isValid()) {
            map.fitBounds(markersGroup.getBounds().pad(0.5));
        }

        /* FIX KRITIS TIMING & BINDING ADMINLTE */
        setTimeout(function () { if (map) map.invalidateSize(); }, 100);
        setTimeout(function () { if (map) map.invalidateSize(); }, 500);

        $(document).on('expanded.lte.pushmenu collapsed.lte.pushmenu', function () {
            if (map) { map.invalidateSize(); }
        });

    } else {
        mapElement.innerHTML = '<div class="alert alert-warning text-center">Map tidak dapat ditampilkan. Pastikan Anda memasukkan kriteria lokasi yang valid.</div>';
    }

    // 2. LOGIC UNTUK MODAL PERMINTAAN
    document.querySelectorAll('.btn-request').forEach(function(button) {
        button.addEventListener('click', function() {
            const pengajarId = this.getAttribute('data-pengajar-id');
            const pengajarName = this.getAttribute('data-pengajar-name');

            document.getElementById('tutorName').textContent = pengajarName;

            document.getElementById('requestForm').action = `/pelajar/requests/${pengajarId}/create`;
        });
    });
});
</script>
@endpush
