<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\DpjpController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PenjaminanController;
use App\Http\Controllers\RegisterShriController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\TempatTidurController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
});

Route::middleware('auth')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('tempat-tidur', [TempatTidurController::class, 'index'])->name('admin.tempat-tidur');

    Route::prefix('register-shri')->group(function () {
        Route::get('masuk', [RegisterShriController::class, 'masukView'])->name('register-shri.masuk.view');
        Route::get('masuk/create', [RegisterShriController::class, 'masukCreate'])->name('register-shri.masuk.create');

        Route::get('pindah', [RegisterShriController::class, 'pindahView'])->name('register-shri.pindah.view');
        Route::get('pindah/create', [RegisterShriController::class, 'pindahCreate'])->name('register-shri.pindah.create');
        Route::get('pindah/daftar', [RegisterShriController::class, 'daftarPasienDirawatPindah'])->name('register-shri.pindah.daftar');

        Route::get('keluar', [RegisterShriController::class, 'keluarView'])->name('register-shri.keluar.view');
        Route::get('keluar/daftar', [RegisterShriController::class, 'daftarPasienDirawatPindah'])->name('register-shri.keluar.daftar');
    });

    Route::prefix('laporan')->group(function () {
        Route::get('kunjungan', [LaporanController::class, 'laporanKunjungan'])->name('laporan.kunjungan');
        Route::get('rekapitulasi', [LaporanController::class, 'rekapitulasi'])->name('laporan.rekapitulasi');


        Route::get("indikator-pelayanan", [LaporanController::class, "indikatorPelayanan"])->name('laporan-indikator');
        Route::get("10-penyakit", [LaporanController::class, "tenDiagnosaPenyakit"])->name('laporan-10-penyakit');

    });

    Route::prefix("data")->group(function () {
        Route::resource('pengguna', PenggunaController::class);
        Route::resource('ruangan', RuanganController::class);
        Route::resource('penjaminan', PenjaminanController::class);
        Route::resource('dpjp', DpjpController::class);
        Route::resource('pasien', PasienController::class);
        Route::resource('diagnosa', DiagnosaController::class);
    });



    // logout


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});
