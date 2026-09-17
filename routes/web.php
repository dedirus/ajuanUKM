<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KemahasiswaanController;
use App\Http\Controllers\LaporanKemahasiswaanController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MasterSatuanController;
use App\Http\Controllers\MasterUkmController;
use App\Http\Controllers\MasterUserController;
use App\Http\Controllers\PembinaController;
use App\Http\Controllers\PengesahanPdfController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login or dashboard
Route::get('/', function () {
    return redirect()->route('login');
});

// Central Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Role: Mahasiswa
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/create', [MahasiswaController::class, 'create'])->name('create');
    Route::post('/pengajuan', [MahasiswaController::class, 'store'])->name('store');
    Route::get('/pengajuan/{id}', [MahasiswaController::class, 'show'])->name('show');
    Route::get('/pengajuan/{id}/edit', [MahasiswaController::class, 'edit'])->name('edit');
    Route::put('/pengajuan/{id}', [MahasiswaController::class, 'update'])->name('update');
    Route::get('/pengajuan/{id}/download', [MahasiswaController::class, 'downloadDokumen'])->name('download');
    Route::get('/pengajuan/{id}/cetak-pengesahan', [PengesahanPdfController::class, 'cetak'])->name('cetak-pengesahan');
    Route::get('/pengajuan/{id}/lpj', [MahasiswaController::class, 'lpjForm'])->name('lpj');
    Route::post('/pengajuan/{id}/lpj', [MahasiswaController::class, 'storeLpj'])->name('lpj.store');
    Route::get('/pengajuan/{id}/lpj/download/{type}', [MahasiswaController::class, 'downloadLpjFile'])->name('lpj.download');
});

// Role: Pembina
Route::middleware(['auth', 'role:pembina'])->prefix('pembina')->name('pembina.')->group(function () {
    Route::get('/dashboard', [PembinaController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/{id}', [PembinaController::class, 'show'])->name('show');
    Route::post('/pengajuan/{id}/verify', [PembinaController::class, 'verify'])->name('verify');
    Route::get('/pengajuan/{id}/download', [PembinaController::class, 'downloadDokumen'])->name('download');
    Route::get('/pengajuan/{id}/cetak-pengesahan', [PengesahanPdfController::class, 'cetak'])->name('cetak-pengesahan');
    Route::get('/pengajuan/{id}/lpj/download/{type}', [PembinaController::class, 'downloadLpjFile'])->name('lpj.download');
});

// Role: Kemahasiswaan
Route::middleware(['auth', 'role:kemahasiswaan'])->prefix('kemahasiswaan')->name('kemahasiswaan.')->group(function () {
    Route::get('/dashboard', [KemahasiswaanController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/{id}', [KemahasiswaanController::class, 'show'])->name('show');
    Route::post('/pengajuan/{id}/detail-items', [KemahasiswaanController::class, 'updateDetailItems'])->name('update-details');
    Route::post('/pengajuan/{id}/approve', [KemahasiswaanController::class, 'approveOverall'])->name('approve');
    Route::post('/pengajuan/{id}/verify-lpj', [KemahasiswaanController::class, 'verifyLpj'])->name('verify-lpj');
    Route::get('/pengajuan/{id}/download', [KemahasiswaanController::class, 'downloadDokumen'])->name('download');
    Route::get('/pengajuan/{id}/cetak-pengesahan', [PengesahanPdfController::class, 'cetak'])->name('cetak-pengesahan');
    Route::get('/pengajuan/{id}/lpj/download/{type}', [KemahasiswaanController::class, 'downloadLpjFile'])->name('lpj.download');

    // Laporan & Statistik Realisasi Usulan UKM
    Route::get('/laporan', [LaporanKemahasiswaanController::class, 'index'])->name('laporan');
    Route::get('/laporan/export-excel', [LaporanKemahasiswaanController::class, 'exportExcel'])->name('laporan.export');
    Route::get('/laporan/{ukm_id}', [LaporanKemahasiswaanController::class, 'detail'])->name('laporan.detail');

    // Manajemen Master Data UKM, Satuan & Pengguna
    Route::resource('master-ukm', MasterUkmController::class)->parameters(['master-ukm' => 'id'])->names('ukm');
    Route::resource('master-satuan', MasterSatuanController::class)->parameters(['master-satuan' => 'id'])->names('satuan');
    Route::resource('users', MasterUserController::class)->parameters(['users' => 'id'])->names('users');
});

// Profile management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
