@extends('layouts.app')

@section('content')

{{-- 1. Content Header AdminLTE --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0 text-dark"><i class="fas fa-history"></i> Riwayat Permintaan Privat</h1>
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

        {{-- Kartu Utama Riwayat --}}
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Daftar Permintaan Anda</h3>
            </div>

            <div class="card-body">
                @if($permintaans->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Belum ada permintaan privat yang diajukan.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pengajar</th>
                                    <th>Jadwal Diinginkan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permintaans as $p)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('pelajar.pengajar.show', $p->pengajar->id) }}" class="text-primary font-weight-bold">
                                                {{ $p->pengajar->user->name ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($p->jadwal_diinginkan)->format('d M Y, H:i') }}</td>
                                        <td>
                                            @php
                                                // Logika penentuan warna status
                                                $statusClass = [
                                                    'pending' => 'badge-warning',
                                                    'accepted' => 'badge-success',
                                                    'rejected' => 'badge-danger',
                                                    'cancelled' => 'badge-secondary',
                                                ][$p->status] ?? 'badge-info';
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ ucfirst($p->status) }}</span>
                                        </td>
                                        <td>
                                            @if($p->status === 'pending')
                                                <form action="{{ route('pelajar.permintaan.cancel', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permintaan ini?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-times"></i> Batalkan
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted text-sm">Tidak ada aksi</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination (Menggunakan template Bootstrap 4 AdminLTE) --}}
                    @if ($permintaans instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-4">
                            {{ $permintaans->links() }}
                        </div>
                    @endif

                @endif
            </div> {{-- /.card-body --}}
        </div> {{-- /.card --}}

    </div> {{-- /.container-fluid --}}
</section>
@endsection
