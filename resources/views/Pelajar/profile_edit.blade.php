@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">Edit Profil</h1>

    <div class="bg-white p-4 rounded shadow">
        <form action="{{ route('pelajar.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="block text-sm">Nama</label>
                <input name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-2 py-1" required>
            </div>

            <div class="mb-3">
                <label class="block text-sm">No Telepon</label>
                <input name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" class="w-full border rounded px-2 py-1">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm">Latitude</label>
                    <input name="latitude" value="{{ old('latitude', $user->latitude) }}" class="w-full border rounded px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm">Longitude</label>
                    <input name="longitude" value="{{ old('longitude', $user->longitude) }}" class="w-full border rounded px-2 py-1">
                </div>
            </div>

            <div class="mt-3">
                <label class="block text-sm">Foto Profil (opsional)</label>
                <input type="file" name="foto_profil" accept="image/*">
                @if($user->foto_profil)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$user->foto_profil) }}" alt="foto" class="w-24 h-24 object-cover rounded">
                    </div>
                @endif
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
                <button type="button" id="geoBtn" class="px-3 py-2 bg-gray-100 rounded">Gunakan Lokasi Saya</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('geoBtn').addEventListener('click', function(){
    if (!navigator.geolocation) { alert('Geolocation tidak didukung di browser ini.'); return; }
    navigator.geolocation.getCurrentPosition(function(position){
        document.querySelector('input[name="latitude"]').value = position.coords.latitude.toFixed(6);
        document.querySelector('input[name="longitude"]').value = position.coords.longitude.toFixed(6);
    }, function(err){
        alert('Gagal mendapatkan lokasi: ' + err.message);
    });
});
</script>
@endsection
