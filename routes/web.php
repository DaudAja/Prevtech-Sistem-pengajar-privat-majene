<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PelajarController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Pelajar routes
Route::prefix('pelajar')->middleware(['auth', 'pelajar'])->group(function () {
    // Dashboard & Profil
    Route::get('/dashboard', [PelajarController::class, 'index'])->name('pelajar.dashboard');
    Route::get('/profile', [PelajarController::class, 'showProfile'])->name('pelajar.profile');
    Route::get('/profile/edit', [PelajarController::class, 'editProfile'])->name('pelajar.profile.edit');
    Route::post('/profile', [PelajarController::class, 'updateProfile'])->name('pelajar.profile.update');

    // Pencarian dan Rekomendasi
    Route::get('/search', [PelajarController::class, 'searchForm'])->name('pelajar.search.form');
    // Proses pencarian dan menampilkan hasil
    Route::match(['get', 'post'], '/search/results', [PelajarController::class, 'search'])->name('pelajar.search.results');

    // Detail Pengajar (Pengajar Show)
    Route::get('/pengajar/{id}', [PelajarController::class, 'showPengajar'])->name('pelajar.pengajar.show');

    // Aksi Permintaan Privat (KOREKSI USE CASE)
    // Rute untuk mengirim permintaan privat
    Route::post('/requests/{pengajarId}/create', [PelajarController::class, 'requestPengajar'])->name('pelajar.pengajar.request');

    // Rute untuk menampilkan riwayat permintaan (Permintaan Index)
    Route::get('/requests/history', [PelajarController::class, 'permintaanIndex'])->name('pelajar.permintaan.index');
    // Rute untuk membatalkan permintaan
    Route::post('/requests/{id}/cancel', [PelajarController::class, 'cancelPermintaan'])->name('pelajar.permintaan.cancel');

    // Rute Ulasan
    Route::get('/reviews/{pengajarId}/create', [PelajarController::class, 'showReviewForm'])->name('pelajar.pengajar.review.form');
    Route::post('/reviews/{pengajarId}', [PelajarController::class, 'storeReview'])->name('pelajar.pengajar.review.store');

    // History Rekomendasi (Jika ingin ditampilkan)
    Route::get('/recommendations/history', [PelajarController::class, 'recommendationsHistory'])->name('pelajar.recommendations.history');
});



// Pengajar routes (minimal fix)
Route::prefix('pengajar')->middleware(['auth', 'pengajar'])->group(function () {
    Route::get('/dashboard', [PengajarController::class, 'index'])->name('pengajar.dashboard');
    Route::get('/profile', [PengajarController::class, 'showProfile'])->name('pengajar.profile');
    Route::post('/profile', [PengajarController::class, 'updateProfile'])->name('pengajar.profile.update');
    Route::get('/requests', [PengajarController::class, 'viewRequests'])->name('pengajar.requests');
    Route::post('/requests/{id}/accept', [PengajarController::class, 'acceptRequest'])->name('pengajar.requests.accept');
    Route::post('/requests/{id}/decline', [PengajarController::class, 'declineRequest'])->name('pengajar.requests.decline');
    Route::get('/sessions', [PengajarController::class, 'viewSessions'])->name('pengajar.sessions');
    Route::get('/reviews', [PengajarController::class, 'viewReviews'])->name('pengajar.reviews');
});

// Admin routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Manage pengajar
    Route::get('/pengajar', [AdminController::class, 'managePengajar'])->name('admin.pengajar');
    Route::post('/pengajar/{id}/verify', [AdminController::class, 'verifyPengajar'])->name('admin.pengajar.verify');
    Route::post('/pengajar/{id}/unverify', [AdminController::class, 'unverifyPengajar'])->name('admin.pengajar.unverify');
    Route::delete('/pengajar/{id}', [AdminController::class, 'deletePengajar'])->name('admin.pengajar.delete');

    // Manage pelajar
    Route::get('/pelajar', [AdminController::class, 'managePelajar'])->name('admin.pelajar');
    Route::delete('/pelajar/{id}', [AdminController::class, 'deletePelajar'])->name('admin.pelajar.delete');

    // Manage rekomendasi
    Route::get('/rekomendasi', [AdminController::class, 'manageRekomendasi'])->name('admin.rekomendasi');
    Route::delete('/rekomendasi/{id}', [AdminController::class, 'deleteRekomendasi'])->name('admin.rekomendasi.delete');

    // Manage permintaan
    Route::get('/permintaan', [AdminController::class, 'managePermintaan'])->name('admin.permintaan');

    // Manage ulasan
    Route::get('/ulasan', [AdminController::class, 'manageUlasan'])->name('admin.ulasan');
    Route::delete('/ulasan/{id}', [AdminController::class, 'deleteUlasan'])->name('admin.ulasan.delete');

    // Reports
    Route::get('/reports', [AdminController::class, 'showReports'])->name('admin.reports');
});
