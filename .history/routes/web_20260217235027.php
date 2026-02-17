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


Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('login'); // Halaman Depan = Login
    Route::get('/login', [AuthController::class, 'index']);
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.proses');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/mitra/import', [App\Http\Controllers\MitraController::class, 'import'])->name('mitra.import');
    Route::get('/mitra/template', [App\Http\Controllers\MitraController::class, 'downloadTemplate'])->name('mitra.template');
    Route::get('/mitra/export', [App\Http\Controllers\MitraController::class, 'export'])->name('mitra.export');
    Route::resource('mitra', \App\Http\Controllers\MitraController::class);
    Route::resource('kegiatan', \App\Http\Controllers\KegiatanController::class);
    Route::post('/kegiatan/hks', [\App\Http\Controllers\KegiatanController::class, 'storeHks'])->name('kegiatan.storeHks');
    Route::delete('/kegiatan/hks/{id}', [\App\Http\Controllers\KegiatanController::class, 'destroyHks'])->name('kegiatan.destroyHks');
    Route::resource('sbml', \App\Http\Controllers\AturanSbmlController::class)->only(['index', 'store', 'destroy']);
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{id}/edit', [TransaksiController::class, 'edit'])->name('transaksi.edit');
    Route::put('/transaksi/{id}', [TransaksiController::class, 'update'])->name('transaksi.update');
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');
    Route::get('/api/hks/{kegiatanId}', [\App\Http\Controllers\TransaksiController::class, 'getHks']);
    Route::resource('spk', SpkController::class);
    Route::get('/spk/{id}/download-word', [SpkWordController::class, 'download'])->name('spk.word');
    Route::get('/api/get-spk-by-kegiatan/{id}', [TransaksiController::class, 'getSpkByKegiatan']);
    Route::get('/api/get-jabatan-by-kegiatan/{id}', [TransaksiController::class, 'getJabatanByKegiatan']);
    Route::resource('hks', \App\Http\Controllers\HksController::class);
    Route::post('/hks', [HksController::class, 'store'])->name('hks.store');
    Route::get('/api/get-hks-by-jabatan', [TransaksiController::class, 'getHksByJabatan']);
    Route::resource('satuan', SatuanController::class);
    Route::resource('jabatan', JabatanController::class);
    Route::resource('tag-kegiatan', TagKegiatanController::class)->names('tag_kegiatan');
    Route::get('/rekap-honor', [TransaksiController::class, 'rekap'])->name('rekap.honor');
    Route::get('/transaksi/print-spk/{nomor_urut}', [TransaksiController::class, 'printSpk'])->name('transaksi.print-spk');
    Route::get('/satker', [SatkerController::class, 'index'])->name('satker.index');
    Route::post('/satker', [SatkerController::class, 'store'])->name('satker.store');   
    Route::resource('peran', PeranController::class);
    Route::resource('pengguna', PenggunaController::class);
    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
    Route::post('/arsip/update', [ArsipController::class, 'updateLink'])->name('arsip.update');

    Route::post('/api/check-honor-limit', [ApiHelperController::class, 'checkHonorLimit'])->name('api.check-honor-limit');


});