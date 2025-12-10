@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Dashboard Pelajar</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="col-span-2">
            <div class="bg-white p-4 rounded shadow">
                <h2 class="font-semibold">Rekomendasi Terbaru</h2>
                @if($recentRecommendations->isEmpty())
                    <p class="text-sm text-gray-600">Belum ada rekomendasi.</p>
                @else
                    <ul>
                        @foreach($recentRecommendations as $rec)
                            <li class="py-2 border-b">
                                <a href="{{ route('pelajar.pengajar.show', $rec->pengajar->id) }}" class="font-medium">
                                    {{ $rec->pengajar->nama ?? $rec->pengajar->user->name }}
                                </a>
                                <div class="text-sm text-gray-600">jarak: {{ $rec->jarak_km }} km — skor: {{ $rec->nilai_kemiripan }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-white p-4 rounded shadow mt-4">
                <h2 class="font-semibold">Riwayat Permintaan</h2>
                @if($recentRequests->isEmpty())
                    <p class="text-sm text-gray-600">Belum ada permintaan.</p>
                @else
                    <ul>
                        @foreach($recentRequests as $p)
                            <li class="py-2 border-b">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <a href="{{ route('pelajar.pengajar.show', $p->pengajar->id) }}" class="font-medium">
                                            {{ $p->pengajar->nama ?? $p->pengajar->user->name }}
                                        </a>
                                        <div class="text-sm text-gray-600">Jadwal: {{ $p->jadwal_diinginkan }}</div>
                                    </div>
                                    <div class="text-sm">{{ ucfirst($p->status) }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="font-semibold">Saran Pengajar</h2>
                @forelse($verifiedPengajars as $peng)
                    <div class="py-2 border-b">
                        <a href="{{ route('pelajar.search.form', $peng->id) }}" class="font-medium">
                            {{ $peng->nama ?? $peng->user->name }}
                        </a>
                        <div class="text-sm text-gray-600">Mapel: {{ $peng->mata_pelajaran }} • Pengalaman: {{ $peng->pengalaman_tahun }} tahun</div>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">Belum ada pengajar terverifikasi.</p>
                @endforelse
            </div>

            <div class="bg-white p-4 rounded shadow mt-4">
                <h3 class="font-semibold mb-2">Cari Pengajar</h3>
                <a href="{{ route('pelajar.search.form') }}" class="inline-block px-3 py-2 bg-blue-600 text-white rounded">Cari sekarang</a>
            </div>
        </div>
    </div>
</div>
@endsection
