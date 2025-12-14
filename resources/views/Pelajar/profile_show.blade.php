@extends('layouts.app')

@section('content')

{{-- 1. Content Header AdminLTE --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0 text-dark"><i class="fas fa-user"></i> Profil Saya</h1>
            </div>
        </div>
    </div>
</div>

{{-- 2. Main Content Section --}}
<section class="content">
    <div class="container-fluid">

        {{-- Flash Messages (Jika ada) --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Kartu Utama Detail Profil --}}
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">Informasi Akun</h3>
            </div>

            {{-- <div class="card-body">
                <div class="row"> --}}
                    {{-- Foto Profil --}}
                    {{-- <div class="col-md-3 text-center mb-3">
                        <div class="bg-gray-100 rounded overflow-hidden" style="width: 120px; height: 120px; margin: 0 auto;">
                            @if($user->foto_profil)
                                <img src="{{ asset('storage/'.$user->foto_profil) }}"
                                     alt="Foto Profil"
                                     class="img-fluid img-circle elevation-2"
                                     style="object-fit: cover; width: 100%; height: 100%;">
                            @else
                                <img src="{{ asset('img/default-user.png') }}"
                                     alt="Default Foto"
                                     class="img-fluid img-circle elevation-2"
                                     style="object-fit: cover; width: 100%; height: 100%;">
                            @endif
                        </div>
                    </div> --}}

                    {{-- Detail Teks --}}
                    <div class="col-md-9">
                        <h2 class="text-xl font-weight-bold text-primary">{{ $user->name }}</h2>

                        <dl class="row mt-3">
                            <dt class="col-sm-3 text-sm">Email</dt>
                            <dd class="col-sm-9 text-sm text-dark">{{ $user->email }}</dd>

                            <dt class="col-sm-3 text-sm">No Telepon</dt>
                            <dd class="col-sm-9 text-sm text-dark">{{ $user->no_telepon ?? '-' }}</dd>

                            <dt class="col-sm-3 text-sm">Lokasi (Koordinat)</dt>
                            <dd class="col-sm-9 text-sm text-dark">
                                {{ $user->latitude ?? '-' }}, {{ $user->longitude ?? '-' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div> {{-- /.card-body --}}

            <div class="card-footer">
                <a href="{{ route('pelajar.profile.edit') }}" class="btn btn-primary">
                    <i class="fas fa-user-edit"></i> Edit Profil
                </a>
            </div> {{-- /.card-footer --}}
        </div> {{-- /.card --}}

    </div> {{-- /.container-fluid --}}
</section>
@endsection
