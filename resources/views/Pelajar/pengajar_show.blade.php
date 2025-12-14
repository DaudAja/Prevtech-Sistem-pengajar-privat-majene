@extends('layouts.app')

@section('content')

{{-- 1. Content Header AdminLTE --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0 text-dark"><i class="fas fa-user-circle"></i> Detail Profil Pengajar</h1>
            </div>
        </div>
    </div>
</div>

{{-- 2. Main Content Section --}}
<section class="content">
    <div class="container-fluid">

        {{-- Tombol Kembali yang sudah diperbaiki --}}
        <div class="row mb-3">
            <div class="col-12">
                {{-- Menggunakan url()->previous() untuk kembali ke halaman hasil pencarian sebelumnya --}}
                <a href="{{ url()->previous() }}" class="btn btn-danger btn-sm text-white">
                    <i class="fas fa-chevron-left"></i> Kembali ke Hasil Pencarian
                </a>
            </div>
        </div>

        {{-- Flash Messages (Jika ada) --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Kartu Utama Detail Pengajar --}}
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">Informasi Dasar</h3>
                <div class="card-tools">
                    @if($pengajar->status_verifikasi)
                        <span class="badge badge-success" title="Pengajar telah diverifikasi"><i class="fas fa-check-circle"></i> Terverifikasi</span>
                    @else
                        <span class="badge badge-warning" title="Menunggu verifikasi"><i class="fas fa-exclamation-triangle"></i> Belum Verifikasi</span>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    {{-- Foto Profil --}}
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-gray-100 rounded overflow-hidden mx-auto" style="width: 150px; height: 150px;">
                            @if(optional($pengajar->user)->foto_profil)
                                <img src="{{ asset('storage/'.optional($pengajar->user)->foto_profil) }}"
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
                    </div>

                    {{-- Detail Teks --}}
                    <div class="col-md-9">
                        <h2 class="text-xl font-weight-bold text-primary">{{ $pengajar->nama ?? $pengajar->user->name }}</h2>
                        <dl class="row mt-3">
                            <dt class="col-sm-3 text-sm">Mata Pelajaran</dt>
                            <dd class="col-sm-9 text-sm text-dark">{{ $pengajar->mata_pelajaran }}</dd>

                            <dt class="col-sm-3 text-sm">Pengalaman</dt>
                            <dd class="col-sm-9 text-sm text-dark">{{ $pengajar->pengalaman_tahun }} tahun</dd>

                            <dt class="col-sm-3 text-sm">Deskripsi Diri</dt>
                            <dd class="col-sm-9 text-sm text-dark">{{ $pengajar->deskripsi }}</dd>
                        </dl>

                        {{-- TOMBOL INI AKAN MEMBUKA MODAL #requestModal --}}
                        <button type="button" class="btn btn-success mt-3"
                            data-toggle="modal"
                            data-target="#requestModal"
                            data-pengajar-id="{{ $pengajar->id }}"
                            data-pengajar-name="{{ $pengajar->user->name }}">
                            <i class="fas fa-calendar-check"></i> Minta Privat Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Ulasan (Sisa kode detail pengajar) --}}
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Ulasan Pengguna ({{ $pengajar->ulasan->count() }})</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($pengajar->ulasan->isEmpty())
                    <p class="text-sm text-muted">Belum ada ulasan dari pelajar.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($pengajar->ulasan as $u)
                            <li class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-sm font-weight-bold">{{ optional($u->user)->name }}</h5>
                                    <small class="text-muted">Rating: {{ $u->rating }}</small>
                                </div>
                                <p class="mb-1 text-sm">{{ $u->komentar }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('pelajar.pengajar.review.form', $pengajar->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-star-half-alt"></i> Beri Ulasan
                </a>
            </div>
        </div>

    </div> {{-- /.container-fluid --}}
</section>
@endsection

{{-- MODAL PERMINTAAN PRIVAT --}}
{{-- PENTING: Jika Anda menggunakan @push('modals') di layouts/app.blade.php, pindahkan blok ini ke sana --}}
<div class="modal fade" id="requestModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Minta Privat dengan <span id="tutorName"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      {{-- ACTION DIISI OLEH JAVASCRIPT --}}
      <form id="requestForm" method="POST">
        @csrf
        <div class="modal-body">
            <div class="form-group">
                <label for="jadwal_diinginkan" class="form-label">Jadwal Diinginkan</label>
                <input type="datetime-local" class="form-control" id="jadwal_diinginkan" name="jadwal_diinginkan" required>
            </div>
            <div class="form-group">
                <label for="keterangan" class="form-label">Keterangan Tambahan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Saya butuh fokus pada bab trigonometri."></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- End Modal --}}


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Memastikan skrip jQuery/Bootstrap dimuat sebelum ini
    if (typeof $ === 'undefined' || typeof bootstrap === 'undefined') {
        console.error("jQuery atau Bootstrap belum dimuat. Tombol Minta Privat mungkin tidak berfungsi.");
        return;
    }

    // LOGIC UNTUK MENGISI DATA MODAL SAAT TOMBOL DIKLIK
    $('#requestModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Tombol yang memicu modal
        var pengajarId = button.data('pengajar-id');
        var pengajarName = button.data('pengajar-name');

        var modal = $(this);

        // 1. Mengisi Nama Pengajar di Judul Modal
        modal.find('#tutorName').text(pengajarName);

        // 2. Mengatur URL Action Form secara dinamis
        // Route: pelajar.requests.create (POST /pelajar/requests/{pengajar}/create)
        var formActionUrl = '{{ url("pelajar/requests") }}/' + pengajarId + '/create';
        modal.find('#requestForm').attr('action', formActionUrl);
    });
});
</script>
@endpush
