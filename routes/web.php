<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\DpjpController;
use App\Http\Controllers\GrafixController;
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
        // masuk
        Route::get('masuk', [RegisterShriController::class, 'masukView'])->name('register-shri.masuk.view');
        Route::get('masuk/create', [RegisterShriController::class, 'masukCreate'])->name('register-shri.masuk.create');
        Route::post('masuk/create', [RegisterShriController::class, 'masukStore'])->name('register-shri.masuk.store');
        Route::get('masuk/edit/{id}', [RegisterShriController::class, 'masukEdit'])->name('register-shri.masuk.edit');
        Route::put('masuk/edit/{id}', [RegisterShriController::class, 'masukUpdate'])->name('register-shri.masuk.update');
        Route::delete('/masuk/{id}', [RegisterShriController::class, 'masukDestroy'])->name('register-shri.masuk.destroy');
        // end masuk



        Route::get('pindah', [RegisterShriController::class, 'pindahView'])->name('register-shri.pindah.view');
        Route::get('pindah/create', [RegisterShriController::class, 'pindahCreate'])->name('register-shri.pindah.create');
        Route::get('pindah/daftar', [RegisterShriController::class, 'daftarPasienDirawatPindah'])->name('register-shri.pindah.daftar');
        Route::post("/pindah", [RegisterShriController::class, "pindahStore"])->name('register-shri.pindah.store');
        Route::delete("/pindah/{id}", [RegisterShriController::class, "pindahDestroy"])->name('register-shri.pindah.destroy');
        Route::get('/pindah/{id}', [RegisterShriController::class, "pindahEdit"])->name('register-shri.pindah.edit');
        Route::put('/pindah/{id}', [RegisterShriController::class, "pindahUpdate"])->name('register-shri.pindah.update');

        Route::get('keluar', [RegisterShriController::class, 'keluarView'])->name('register-shri.keluar.view');
        Route::get('keluar/daftar', [RegisterShriController::class, 'daftarPasienDirawatKeluar'])->name('register-shri.keluar.daftar');
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


    Route::prefix('grafik')->group(function () {
        Route::get("kunjungan-pasien", [GrafixController::class, "kunjungan"]);
    });

    // logout


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});
