<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegisterShriController;
use App\Http\Controllers\TempatTidurController;
use Illuminate\Support\Facades\Route;


Route::get("/", [AuthController::class, "login"]);
Route::get("/dashboard", [DashboardController::class, "index"]);
Route::get("/tempat-tidur", [TempatTidurController::class, "index"]);


Route::prefix("register-shri")->group(function () {
    Route::get("masuk", [RegisterShriController::class, "masukView"]);
    Route::get("masuk/create", [RegisterShriController::class, "masukCreate"])->name('form.masuk');

    Route::get("pindah", [RegisterShriController::class, "pindahView"]);
    Route::get("pindah/create", [RegisterShriController::class, "pindahCreate"])->name('form.pindah');

    Route::get("keluar", [RegisterShriController::class, "keluarView"]);


});
