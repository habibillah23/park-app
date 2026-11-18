<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AreaParkirController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\RekapTransaksiController;
use App\Http\Controllers\TarifParkirController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ------ Publik: halaman landing booking ------
Route::get('/', [BookingController::class, 'landing'])->name('home');

// ------ Guest routes ------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// ------ Pelanggan: booking dan tiket ------
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/buat', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}/tiket', [BookingController::class, 'tiket'])->name('booking.tiket');
    Route::post('/booking/{booking}/batal', [BookingController::class, 'batal'])->name('booking.batal');
});

// ------ Authenticated routes (any role) ------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ------ Admin only ------
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('tarif', TarifParkirController::class)->except(['show']);
        Route::resource('area', AreaParkirController::class)->except(['show']);
        Route::resource('kendaraan', KendaraanController::class)->except(['show']);
        Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
    });

    // ------ Petugas only ------
    Route::middleware('role:petugas')->group(function () {
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/masuk', [TransaksiController::class, 'create'])->name('transaksi.create');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi/{transaksi}/keluar', [TransaksiController::class, 'keluarForm'])->name('transaksi.keluar-form');
        Route::post('/transaksi/{transaksi}/keluar', [TransaksiController::class, 'keluar'])->name('transaksi.keluar');
        Route::get('/transaksi/{transaksi}/struk', [TransaksiController::class, 'struk'])->name('transaksi.struk');

        // Verifikasi kode booking (tiket QR pelanggan)
        Route::get('/petugas/verifikasi', [PetugasController::class, 'verifikasiForm'])->name('petugas.verifikasi');
        Route::post('/petugas/verifikasi', [PetugasController::class, 'verifikasi'])->name('petugas.verifikasi.cek');
    });

    // ------ Owner only ------
    Route::middleware('role:owner')->group(function () {
        Route::get('/rekap', [RekapTransaksiController::class, 'index'])->name('rekap.index');
    });
});