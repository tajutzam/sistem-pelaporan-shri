<?php

use App\Http\Controllers\APIController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GrafixController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\RegisterShriController;
use App\Http\Controllers\RuanganController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/pasien/search', [PasienController::class, 'search']);
Route::get('/get-ruangan-by-kelas/{kelas}', [RuanganController::class, 'getByKelas']);


Route::post('barber-johnson-data', [GrafixController::class, 'getData'])->name('barber-johnson.data');
Route::post('dashboard-chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');


Route::get('notifications', [DashboardController::class, "notificationToday"])->name('notification');

Route::post('/notifications/{id}/read', [DashboardController::class, 'markAsRead']);

// Mark all notifications as read
Route::post('/notifications/mark-all-read', [DashboardController::class, 'markAllAsRead']);

// Get unread notification count
Route::get('/notifications/unread-count', [DashboardController::class, 'getUnreadCount']);




Route::get('check/verified', [DashboardController::class, "checkIsVerified"])->name('check-laporan');


Route::get("diagnosas", [APIController::class, "searchDiagnosa"])->name('api.diagnosa.search');
