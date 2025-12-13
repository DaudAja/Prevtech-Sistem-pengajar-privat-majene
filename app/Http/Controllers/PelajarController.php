<?php

namespace App\Http\Controllers;

use App\Models\Pengajar;
use App\Models\Permintaan;
use App\Models\Rekomendasi;
use App\Models\Ulasan;
use App\Services\KnnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PelajarController extends \App\Http\Controllers\Controller
{
    public function __construct()
    {
        // pastikan route middleware 'auth' dan 'pelajar' terdaftar di Kernel
        $this->middleware(['auth', 'pelajar']);
    }

    /**
     * Dashboard pelajar: ringkasan rekomendasi terakhir, permintaan, dan daftar pengajar terverifikasi
     */
    public function index()
    {
        $user = Auth::user();

        $recentRecommendations = Rekomendasi::where('user_id', $user->id)
            ->with('pengajar')
            ->orderByDesc('tanggal_rekomendasi')
            ->limit(10)
            ->get();

        $recentRequests = Permintaan::where('user_id', $user->id)
            ->with('pengajar')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // show some verified pengajars as suggestion
        $verifiedPengajars = Pengajar::where('status_verifikasi', true)
            ->orderByDesc('pengalaman_tahun')
            ->limit(8)
            ->get();

        return view('pelajar.dashboard', compact('user', 'recentRecommendations', 'recentRequests', 'verifiedPengajars'));
    }

    /**
     * Show search form
     */
    public function searchForm()
    {
        return view('pelajar.search');
    }

    /**
     * Handle search + rekomendasi (memanggil KnnService)
     */
    public function search(Request $request)
    {
        $data = $request->validate([
            'mata_pelajaran' => 'nullable|string|max:191',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'k' => 'nullable|integer|min:1|max:50',
        ]);

        $criteria = [
            'latitude' => (float)$data['latitude'],
            'longitude' => (float)$data['longitude'],
            'mata_pelajaran' => $data['mata_pelajaran'] ?? null,
        ];

        $k = $data['k'] ?? 5;

        // panggil service KNN (harus dibuat di app/Services/KnnService.php seperti sebelumnya)
        $results = KnnService::recommend($criteria, $k); // hasil: array of ['pengajar','distance','score']

        // simpan history rekomendasi (opsional)
        foreach ($results as $r) {
            Rekomendasi::create([
                'user_id' => Auth::id(),
                'pengajar_id' => $r['pengajar']->id,
                'nilai_kemiripan' => $r['score'],
                'jarak_km' => $r['distance'],
                'tanggal_rekomendasi' => now(),
            ]);
        }

        return view('pelajar.search_results', [
            'results' => $results,
            'criteria' => $criteria
        ]);
    }

    /**
     * View detail pengajar
     */
    public function showPengajar($id)
    {
        $pengajar = Pengajar::with(['user', 'ulasan'])->findOrFail($id);
        return view('pelajar.pengajar_show', compact('pengajar'));
    }

    /**
     * Create a request/booking (permintaan) from pelajar to pengajar
     */
    public function requestPengajar(Request $request, $pengajarId)
    {
        $pengajar = Pengajar::findOrFail($pengajarId);

        $data = $request->validate([
            // Tambahkan validasi yang lebih ketat sesuai form modal
            'jadwal_diinginkan' => 'required|date|after:now',
            'keterangan' => 'nullable|string|max:1500',
            // 'contact' yang lama dihapus/diasumsikan diambil dari profil User
        ]);

        // Mengambil mata pelajaran dari profil Pengajar untuk disimpan di Permintaan
        $mataPelajaran = $pengajar->mata_pelajaran;

        $perm = Permintaan::create([
            'user_id' => Auth::id(),
            'pengajar_id' => $pengajar->id,
            'mata_pelajaran' => $mataPelajaran, // Field mata_pelajaran perlu diisi
            'jadwal_diinginkan' => $data['jadwal_diinginkan'],
            'keterangan' => $data['keterangan'] ?? null,
            'status' => 'pending'
        ]);

        // (optional) TODO: kirim notifikasi ke pengajar (email / in-app)
        // Arahkan ke riwayat permintaan, bukan dashboard
        return redirect()->route('pelajar.permintaan.index')->with('success', 'Permintaan berhasil dikirim kepada '.$pengajar->user->name.'. Tunggu konfirmasi pengajar.');
    }

    /**
     * List permintaan (riwayat booking)
     */
    public function permintaanIndex()
    {
        $permintaans = Permintaan::where('user_id', Auth::id())
            ->with('pengajar')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('pelajar.permintaan_index', compact('permintaans'));
    }

    /**
     * Cancel a pending permintaan (only by owner and if status pending)
     */
    public function cancelPermintaan($id)
    {
        $perm = Permintaan::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $perm->status = 'cancelled';
        $perm->save();

        return redirect()->back()->with('success', 'Permintaan dibatalkan.');
    }

    /**
     * Show review form for a pengajar after a completed session
     */
    public function showReviewForm($pengajarId)
    {
        $pengajar = Pengajar::findOrFail($pengajarId);
        return view('pelajar.review_form', compact('pengajar'));
    }

    /**
     * Store review
     */
    public function storeReview(Request $request, $pengajarId)
    {
        $pengajar = Pengajar::findOrFail($pengajarId);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'komentar' => 'nullable|string|max:2000',
        ]);

        Ulasan::create([
            'user_id' => Auth::id(),
            'pengajar_id' => $pengajar->id,
            'rating' => $data['rating'],
            'komentar' => $data['komentar'] ?? null,
        ]);

        return redirect()->route('pelajar.pengajar.show', $pengajar->id)->with('success', 'Ulasan berhasil dikirim.');
    }

    /**
     * Show profile page (read-only)
     */
    public function showProfile()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan login.');
        }

        // jika pelajar = user, cukup tampilkan data user
        return view('pelajar.profile_show', compact('user'));
    }

    /**
     * Edit profile form
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('pelajar.profile_edit', compact('user'));
    }

    /**
     * Update profile (including foto upload). Uses storage:link (public storage).
     */
    public function updateProfile(Request $request)
    {
        $user = \App\Models\User::findOrFail(Auth::id());

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'no_telepon' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto_profil' => 'nullable|image|max:2048', // max 2MB
        ]);

        if ($request->hasFile('foto_profil')) {
            // hapus file lama jika ada
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $path = $request->file('foto_profil')->store('pelajar/profil', 'public');
            $data['foto_profil'] = $path;
        }

        $user->update($data);

        return redirect()->route('pelajar.profile.edit')->with('success', 'Profil diperbarui.');
    }

    /**
     * Show recommendation history
     */
    public function recommendationsHistory()
    {
        $rekomendasi = Rekomendasi::where('user_id', Auth::id())
            ->with('pengajar')
            ->orderByDesc('tanggal_rekomendasi')
            ->paginate(15);

        return view('pelajar.rekomendasi_index', compact('rekomendasi'));
    }
}
