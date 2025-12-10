<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengajar;
use App\Models\Rekomendasi;
use App\Models\Permintaan;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_pelajar' => User::where('role', 'pelajar')->count(),
            'total_pengajar' => User::where('role', 'pengajar')->count(),
            'total_rekomendasi' => Rekomendasi::count(),
            'total_permintaan' => Permintaan::count(),
            'pending_verifikasi' => Pengajar::where('status_verifikasi', false)->count(),
            'total_ulasan' => Ulasan::count(),
        ];

        $recentActivities = Permintaan::with(['user', 'pengajar.user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentActivities'));
    }

    /**
     * Manage pengajar
     */
    public function managePengajar()
    {
        $pengajar = Pengajar::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pengajar', compact('pengajar'));
    }

    /**
     * Verify pengajar
     */
    public function verifyPengajar($id)
    {
        $pengajar = Pengajar::findOrFail($id);
        $pengajar->update(['status_verifikasi' => true]);

        return back()->with('success', 'Pengajar berhasil diverifikasi.');
    }

    /**
     * Unverify pengajar
     */
    public function unverifyPengajar($id)
    {
        $pengajar = Pengajar::findOrFail($id);
        $pengajar->update(['status_verifikasi' => false]);

        return back()->with('success', 'Verifikasi pengajar dibatalkan.');
    }

    /**
     * Delete pengajar
     */
    public function deletePengajar($id)
    {
        $pengajar = Pengajar::findOrFail($id);
        $user = $pengajar->user;

        $pengajar->delete();
        $user->delete();

        return back()->with('success', 'Pengajar berhasil dihapus.');
    }

    /**
     * Manage pelajar
     */
    public function managePelajar()
    {
        $pelajar = User::where('role', 'pelajar')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pelajar', compact('pelajar'));
    }

    /**
     * Delete pelajar
     */
    public function deletePelajar($id)
    {
        $pelajar = User::where('role', 'pelajar')->findOrFail($id);
        $pelajar->delete();

        return back()->with('success', 'Pelajar berhasil dihapus.');
    }

    /**
     * Manage rekomendasi
     */
    public function manageRekomendasi()
    {
        $rekomendasi = Rekomendasi::with(['user', 'pengajar.user'])
            ->orderBy('tanggal_rekomendasi', 'desc')
            ->paginate(20);

        return view('admin.rekomendasi', compact('rekomendasi'));
    }

    /**
     * Delete rekomendasi
     */
    public function deleteRekomendasi($id)
    {
        $rekomendasi = Rekomendasi::findOrFail($id);
        $rekomendasi->delete();

        return back()->with('success', 'Rekomendasi berhasil dihapus.');
    }

    /**
     * Manage permintaan
     */
    public function managePermintaan()
    {
        $permintaan = Permintaan::with(['user', 'pengajar.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.permintaan', compact('permintaan'));
    }

    /**
     * Manage ulasan
     */
    public function manageUlasan()
    {
        $ulasan = Ulasan::with(['user', 'pengajar.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.ulasan', compact('ulasan'));
    }

    /**
     * Delete ulasan
     */
    public function deleteUlasan($id)
    {
        $ulasan = Ulasan::findOrFail($id);
        $ulasan->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }

    /**
     * Show reports/statistics
     */
    public function showReports()
    {
        // Statistics by month
        $monthlyStats = [
            'registrations' => User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray(),
            'recommendations' => Rekomendasi::selectRaw('MONTH(tanggal_rekomendasi) as month, COUNT(*) as count')
                ->whereYear('tanggal_rekomendasi', date('Y'))
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray(),
        ];

        // Top rated pengajar
        $topPengajar = Pengajar::with('user')
            ->withCount('ulasan')
            ->withAvg('ulasan', 'rating')
            ->orderBy('ulasan_avg_rating', 'desc')
            ->limit(10)
            ->get();

        // Popular mata pelajaran
        $popularSubjects = Pengajar::selectRaw('mata_pelajaran, COUNT(*) as count')
            ->groupBy('mata_pelajaran')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports', compact('monthlyStats', 'topPengajar', 'popularSubjects'));
    }
}
