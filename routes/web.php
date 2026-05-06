<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', function () {
        return view('karyawan.dashboard');
    })->name('karyawan.dashboard');
});

Route::middleware(['auth', 'role:admin,karyawan'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('produk', \App\Http\Controllers\ProdukController::class);
    Route::get('/pembelian/export', [PembelianController::class, 'export'])->name('pembelian.export');
    Route::get('/penjualan/export', [PenjualanController::class, 'export'])->name('penjualan.export');
    Route::resource('pembelian', \App\Http\Controllers\PembelianController::class);
    Route::resource('penjualan', \App\Http\Controllers\PenjualanController::class);
    Route::resource('batch', \App\Http\Controllers\BatchController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

require __DIR__ . '/auth.php';
