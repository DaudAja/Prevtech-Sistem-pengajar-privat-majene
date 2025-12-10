@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">Riwayat Permintaan</h1>

    <div class="bg-white p-4 rounded shadow">
        @if($permintaans->isEmpty())
            <p class="text-sm text-gray-600">Belum ada permintaan.</p>
        @else
            <ul>
                @foreach($permintaans as $p)
                    <li class="py-3 border-b flex justify-between items-center">
                        <div>
                            <a href="{{ route('pelajar.pengajar.show', $p->pengajar->id) }}" class="font-medium">
                                {{ $p->pengajar->nama ?? $p->pengajar->user->name }}
                            </a>
                            <div class="text-sm text-gray-600">Jadwal: {{ $p->jadwal_diinginkan }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm">{{ ucfirst($p->status) }}</span>
                            @if($p->status === 'pending')
                                <form action="{{ route('pelajar.permintaan.cancel', $p->id) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-1 text-sm bg-red-500 text-white rounded">Batalkan</button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4">
                {{ $permintaans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
