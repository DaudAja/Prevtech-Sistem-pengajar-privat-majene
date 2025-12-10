@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">Cari & Dapatkan Rekomendasi</h1>

    <div class="bg-white p-4 rounded shadow">
        <form action="{{ route('pelajar.search.results') }}" method="POST" id="searchForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm">Mata Pelajaran</label>
                    <input name="mata_pelajaran" class="w-full border rounded px-2 py-1" placeholder="Contoh: Matematika">
                </div>
                <div>
                    <label class="block text-sm">Latitude</label>
                    <input name="latitude" id="lat" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label class="block text-sm">Longitude</label>
                    <input name="longitude" id="lon" class="w-full border rounded px-2 py-1" required>
                </div>
            </div>

            <div class="mt-3 flex items-center gap-3">
                <button type="button" id="geoBtn" class="px-3 py-2 border rounded bg-gray-100">Gunakan lokasi saya</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Cari</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('geoBtn').addEventListener('click', function(){
    if (!navigator.geolocation) { alert('Geolocation tidak didukung di browser ini.'); return; }
    navigator.geolocation.getCurrentPosition(function(position){
        document.getElementById('lat').value = position.coords.latitude.toFixed(6);
        document.getElementById('lon').value = position.coords.longitude.toFixed(6);
    }, function(err){
        alert('Gagal mendapatkan lokasi: ' + err.message);
    });
});
</script>
@endsection
