@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <a href="{{ route('pelajar.search.form') }}" class="text-sm text-blue-600 mb-4 inline-block">« Kembali</a>

    <div class="bg-white p-4 rounded shadow">
        <div class="flex gap-4">
            <div class="w-24 h-24 bg-gray-100 rounded overflow-hidden">
                @if(optional($pengajar->user)->foto_profil)
                    <img src="{{ asset('storage/'.optional($pengajar->user)->foto_profil) }}" alt="foto" class="w-full h-full object-cover">
                @endif
            </div>
            <div>
                <h2 class="text-xl font-semibold">{{ $pengajar->nama ?? $pengajar->user->name }}</h2>
                <div class="text-sm text-gray-600">Mapel: {{ $pengajar->mata_pelajaran }}</div>
                <div class="text-sm text-gray-600">Pengalaman: {{ $pengajar->pengalaman_tahun }} tahun</div>
                <div class="mt-2">{{ $pengajar->deskripsi }}</div>
            </div>
        </div>

        <div class="mt-4">
            <h3 class="font-semibold">Ulasan</h3>
            @if($pengajar->ulasan->isEmpty())
                <p class="text-sm text-gray-600">Belum ada ulasan.</p>
            @else
                <ul>
                    @foreach($pengajar->ulasan as $u)
                        <li class="py-2 border-b">
                            <div class="text-sm font-medium">{{ optional($u->user)->name }}</div>
                            <div class="text-sm text-gray-600">Rating: {{ $u->rating }}</div>
                            <div class="text-sm">{{ $u->komentar }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('pelajar.pengajar.review.form', $pengajar->id) }}" class="px-3 py-2 bg-yellow-500 text-white rounded">Beri Ulasan</a>
        </div>
    </div>
</div>
@endsection
