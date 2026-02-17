<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\AturanSbmlController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\SpkWordController;
use App\Http\Controllers\HksController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\TagKegiatanController;
use App\Http\Controllers\SatkerController;
use App\Http\Controllers\PeranController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\ApiHelperController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::get('/login', [AuthController::class, 'index']);
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.proses');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Mitra (dengan fitur tambahan)
    Route::post('/mitra/import', [MitraController::class, 'import'])->name('mitra.import');
    Route::get('/mitra/template', [MitraController::class, 'downloadTemplate'])->name('mitra.template');
    Route::get('/mitra/export', [MitraController::class, 'export'])->name('mitra.export');
    Route::resource('mitra', MitraController::class);

    // Kegiatan (dengan rute Hks internal)
    Route::resource('kegiatan', KegiatanController::class);
    Route::post('/kegiatan/hks', [KegiatanController::class, 'storeHks'])->name('kegiatan.storeHks');
    Route::delete('/kegiatan/hks/{id}', [KegiatanController::class, 'destroyHks'])->name('kegiatan.destroyHks');

    // SBML
    Route::resource('sbml', AturanSbmlController::class)->only(['index', 'store', 'destroy']);

    // === TRANSAKSI (Dikelompokkan agar rapi & Fix Rekap) ===
    // Perbaikan: Nama route diubah ke transaksi.rekap agar sinkron dengan Dashboard
    Route::get('/transaksi/rekap', [TransaksiController::class, 'rekap'])->name('transaksi.rekap');
    Route::get('/transaksi/print-spk/{nomor_urut}', [TransaksiController::class, 'printSpk'])->name('transaksi.print-spk');
    
    // Resource Transaksi
    Route::resource('transaksi', TransaksiController::class);

    // SPK & Cetak
    Route::resource('spk', SpkController::class);
    Route::get('/spk/{id}/download-word', [SpkWordController::class, 'download'])->name('spk.word');

    // HKS & Master Data
    Route::resource('hks', HksController::class);
    Route::resource('satuan', SatuanController::class);
    Route::resource('jabatan', JabatanController::class);
    Route::resource('tag-kegiatan', TagKegiatanController::class)->names('tag_kegiatan');

    // Satker
    Route::get('/satker', [SatkerController::class, 'index'])->name('satker.index');
    Route::post('/satker', [SatkerController::class, 'store'])->name('satker.store');

    // Manajemen Pengguna
    Route::resource('peran', PeranController::class);
    Route::resource('pengguna', PenggunaController::class);

    // Arsip Digital
    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
    Route::post('/arsip/update', [ArsipController::class, 'updateLink'])->name('arsip.update');

    // === API HELPERS (Untuk AJAX Form) ===
    Route::get('/api/hks/{kegiatanId}', [TransaksiController::class, 'getHks']);
    Route::get('/api/get-spk-by-kegiatan/{id}', [TransaksiController::class, 'getSpkByKegiatan']);
    Route::get('/api/get-jabatan-by-kegiatan/{id}', [TransaksiController::class, 'getJabatanByKegiatan']);
    Route::get('/api/get-hks-by-jabatan', [TransaksiController::class, 'getHksByJabatan']);
    Route::post('/api/check-honor-limit', [ApiHelperController::class, 'checkHonorLimit'])->name('api.check-honor-limit');
});