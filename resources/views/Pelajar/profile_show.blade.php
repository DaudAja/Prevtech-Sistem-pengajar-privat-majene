@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">Profil Saya</h1>

    <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center gap-4">
            <div class="w-24 h-24 bg-gray-100 rounded overflow-hidden">
                @if($user->foto_profil)
                    <img src="{{ asset('storage/'.$user->foto_profil) }}" alt="foto" class="w-full h-full object-cover">
                @endif
            </div>
            <div>
                <div class="text-lg font-semibold">{{ $user->name }}</div>
                <div class="text-sm text-gray-600">{{ $user->email }}</div>
                <div class="text-sm text-gray-600">No: {{ $user->no_telepon ?? '-' }}</div>
                <div class="text-sm text-gray-600">Koordinat: {{ $user->latitude ?? '-' }}, {{ $user->longitude ?? '-' }}</div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('pelajar.profile.edit') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Edit Profil</a>
        </div>
    </div>
</div>
@endsection
