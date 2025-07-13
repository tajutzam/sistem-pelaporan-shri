<?php

use App\Http\Controllers\PasienController;
use App\Http\Controllers\RuanganController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/pasien/search', [PasienController::class, 'search']);
Route::get('/get-ruangan-by-kelas/{kelas}', [RuanganController::class, 'getByKelas']);

