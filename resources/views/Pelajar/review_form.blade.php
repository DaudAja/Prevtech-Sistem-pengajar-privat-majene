@extends('layouts.app')

@section('content')

{{-- 1. Content Header AdminLTE --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0 text-dark"><i class="fas fa-star"></i> Tulis Ulasan untuk {{ optional($pengajar->user)->name ?? 'Pengajar' }}</h1>
            </div>
        </div>
    </div>
</div>

{{-- 2. Main Content Section --}}
<section class="content">
    <div class="container-fluid">

        {{-- Tombol Kembali --}}
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('pelajar.pengajar.show', $pengajar->id) }}" class="btn btn-default btn-sm">
                    <i class="fas fa-chevron-left"></i> Kembali ke Detail Pengajar
                </a>
            </div>
        </div>

        {{-- Flash Messages (Opsional, jika belum diatur di master layout) --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- Kartu Formulir Ulasan --}}
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">Berikan Penilaian Anda</h3>
            </div>

            <form action="{{ route('pelajar.pengajar.review.store', $pengajar->id) }}" method="POST">
                @csrf
                <div class="card-body">

                    {{-- Input Rating --}}
                    <div class="form-group">
                        <label for="rating">Rating (1 - 5) <span class="text-danger">*</span></label>
                        <select name="rating" id="rating" class="form-control" required style="width: 120px;">
                            @for($i=5;$i>=1;$i--)
                                <option value="{{ $i }}">{{ $i }} Bintang</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Input Komentar --}}
                    <div class="form-group">
                        <label for="komentar">Komentar <span class="text-muted">(Opsional)</span></label>
                        <textarea name="komentar" id="komentar" class="form-control" rows="5" placeholder="Tulis ulasan Anda di sini..."></textarea>
                    </div>

                </div> {{-- /.card-body --}}

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Kirim Ulasan
                    </button>
                </div> {{-- /.card-footer --}}
            </form>
        </div> {{-- /.card --}}

    </div> {{-- /.container-fluid --}}
</section>
@endsection
