<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pengajar;
use App\Models\Permintaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PengajarController extends Controller
{
    public function __construct()
    {
        // parent::__construct();
        // $this->middleware('auth');
    }

    /**
     * Show pengajar dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $pengajar = $user->pengajar;

        if (!$pengajar) {
            return redirect('/')->with('error', 'Profil pengajar tidak ditemukan.');
        }

        $pendingRequests = Permintaan::where('pengajar_id', $pengajar->id)
            ->pending()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $acceptedRequests = Permintaan::where('pengajar_id', $pengajar->id)
            ->accepted()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'total_ulasan' => $pengajar->total_ulasan,
            'average_rating' => $pengajar->average_rating,
            'pending_requests' => $pendingRequests->count(),
            'accepted_requests' => Permintaan::where('pengajar_id', $pengajar->id)->accepted()->count(),
        ];

        return view('pengajar.dashboard', compact('user', 'pengajar', 'pendingRequests', 'acceptedRequests', 'stats'));
    }

    /**
     * Show profile page
     */
    public function showProfile()
    {
        $user = Auth::user();
        $pengajar = $user->pengajar;

        return view('pengajar.profil', compact('user', 'pengajar'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $pengajar = $user->pengajar;

        $request->validate([
            'name' => 'required|string|max:255',
            'umur' => 'nullable|integer|min:18|max:100',
            'alamat' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'no_telepon' => 'required|string|max:20',
            'mata_pelajaran' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'pengalaman_tahun' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'tarif_per_jam' => 'required|numeric|min:0',
            'keahlian_khusus' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'sertifikat' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        // Update user data
        $userData = $request->only(['name', 'umur', 'alamat', 'latitude', 'longitude', 'no_telepon']);

        // Handle foto profil upload
        if ($request->hasFile('foto_profil')) {
            // Delete old photo if exists
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $path = $request->file('foto_profil')->store('profil', 'public');
            $userData['foto_profil'] = $path;
        }

        User::where('id', $user->id)->update($userData);

        // Update pengajar data
        $pengajarData = $request->only([
            'mata_pelajaran',
            'pendidikan_terakhir',
            'pengalaman_tahun',
            'deskripsi',
            'tarif_per_jam',
            'keahlian_khusus'
        ]);

        // Handle sertifikat upload
        if ($request->hasFile('sertifikat')) {
            // Delete old certificate if exists
            if ($pengajar->sertifikat) {
                Storage::disk('public')->delete($pengajar->sertifikat);
            }

            $path = $request->file('sertifikat')->store('sertifikat', 'public');
            $pengajarData['sertifikat'] = $path;
        }

        $pengajar->update($pengajarData);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Show list of permintaan
     */
    public function showPermintaan()
    {
        $user = Auth::user();
        $pengajar = $user->pengajar;

        $permintaan = Permintaan::where('pengajar_id', $pengajar->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pengajar.permintaan', compact('permintaan'));
    }

    /**
     * Confirm permintaan (accept or reject)
     */
    public function confirmPermintaan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'catatan_pengajar' => 'nullable|string',
        ]);

        $permintaan = Permintaan::findOrFail($id);

        // Verify that this permintaan belongs to current pengajar
        if ($permintaan->pengajar_id !== Auth::user()->pengajar->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        $permintaan->update([
            'status' => $request->status,
            'catatan_pengajar' => $request->catatan_pengajar,
        ]);

        $message = $request->status === 'diterima'
            ? 'Permintaan berhasil diterima.'
            : 'Permintaan berhasil ditolak.';

        return back()->with('success', $message);
    }

    /**
     * Show jadwal (accepted requests)
     */
    public function showJadwal()
    {
        $user = Auth::user();
        $pengajar = $user->pengajar;

        $jadwal = Permintaan::where('pengajar_id', $pengajar->id)
            ->accepted()
            ->with('user')
            ->orderBy('jadwal_diinginkan', 'asc')
            ->paginate(15);

        return view('pengajar.jadwal', compact('jadwal'));
    }

    /**
     * Show ulasan received
     */
    public function showUlasan()
    {
        $user = Auth::user();
        $pengajar = $user->pengajar;

        $ulasan = $pengajar->ulasan()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pengajar.ulasan', compact('ulasan', 'pengajar'));
    }
}
