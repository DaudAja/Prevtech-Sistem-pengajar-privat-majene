@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container mx-auto p-6">
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="text-2xl font-semibold mb-6">Dashboard Admin</h1>

    {{-- Statistik singkat --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Pelajar</div>
            <div class="text-3xl font-bold">{{ $stats['total_pelajar'] }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Pengajar</div>
            <div class="text-3xl font-bold">{{ $stats['total_pengajar'] }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Pending Verifikasi Pengajar</div>
            <div class="text-3xl font-bold text-red-600">{{ $stats['pending_verifikasi'] }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Rekomendasi</div>
            <div class="text-3xl font-bold">{{ $stats['total_rekomendasi'] }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Permintaan</div>
            <div class="text-3xl font-bold">{{ $stats['total_permintaan'] }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Ulasan</div>
            <div class="text-3xl font-bold">{{ $stats['total_ulasan'] }}</div>
        </div>
    </div>

    {{-- Aktivitas terbaru --}}
    <div class="bg-white rounded shadow p-4">
        <h2 class="text-xl font-medium mb-3">Aktivitas Terbaru (Permintaan)</h2>
        @if($recentActivities->isEmpty())
            <p class="text-gray-600">Belum ada aktivitas permintaan.</p>
        @else
            <table class="w-full table-auto">
                <thead>
                    <tr class="text-left">
                        <th class="px-4 py-2">Waktu</th>
                        <th class="px-4 py-2">Pelajar</th>
                        <th class="px-4 py-2">Pengajar (jika ada)</th>
                        <th class="px-4 py-2">Mata Pelajaran</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentActivities as $item)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $item->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-2">{{ $item->user->name ?? '—' }}</td>
                            <td class="px-4 py-2">{{ optional($item->pengajar->user)->name ?? 'Belum dipilih' }}</td>
                            <td class="px-4 py-2">{{ $item->mata_pelajaran ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $item->status ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
