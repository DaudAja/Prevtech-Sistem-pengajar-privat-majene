@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">Hasil Rekomendasi</h1>

    <div class="bg-white p-4 rounded shadow">
        <div class="mb-4 text-sm text-gray-600">
            Hasil untuk: <strong>{{ $criteria['mata_pelajaran'] ?? 'Semua mapel' }}</strong>
        </div>

        @if(empty($results))
            <p class="text-sm text-gray-600">Tidak ada hasil.</p>
        @else
            <ul>
                @foreach($results as $r)
                    @php $p = $r['pengajar']; @endphp
                    <li class="py-3 border-b flex justify-between items-center">
                        <div>
                            <a href="{{ route('pelajar.pengajar.show', $p->id) }}" class="font-semibold">{{ $p->nama ?? $p->user->name }}</a>
                            <div class="text-sm text-gray-600">Mapel: {{ $p->mata_pelajaran }} • Pengalaman: {{ $p->pengalaman_tahun }} tahun</div>
                            <div class="text-sm text-gray-600">Jarak: {{ $r['distance'] }} km • Skor: {{ $r['score'] }}</div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <form action="{{ route('pelajar.pengajar.request', $p->id) }}" method="POST">
                                @csrf
                                <!-- bisa diarahkan ke form di modal; untuk simplisitas langsung ke route -->
                                <input type="hidden" name="jadwal_diinginkan" value="{{ now()->addDays(1)->toDateString() }}">
                                <button class="px-3 py-1 bg-green-600 text-white rounded">Minta</button>
                            </form>
                            <a href="{{ route('pelajar.pengajar.show', $p->id) }}" class="text-sm text-blue-600">Detail</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
