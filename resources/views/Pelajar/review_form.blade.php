@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">Tulis Ulasan</h1>

    <div class="bg-white p-4 rounded shadow">
        <form action="{{ route('pelajar.pengajar.review.store', $pengajar->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-sm">Rating (1-5)</label>
                <select name="rating" class="w-24 border rounded px-2 py-1">
                    @for($i=5;$i>=1;$i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-sm">Komentar</label>
                <textarea name="komentar" class="w-full border rounded px-2 py-1" rows="5"></textarea>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Kirim Ulasan</button>
        </form>
    </div>
</div>
@endsection
