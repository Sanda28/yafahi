<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Superadmin\AdminController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LiburController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\JadwalController;
// =========================
// Public Routes (Tanpa Login)
// =========================

Route::get('/', [HomeController::class, 'index'])->name('landing');

// Scan QR lewat HP (tanpa login)
Route::get('/scan/{hash}', [AbsensiController::class, 'scanQR'])->name('absensi.scan');


// =========================
// Routes dengan Login (auth)
// =========================
Route::middleware(['auth'])->group(function () {

    // ===== Dashboard =====
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===== Profile =====
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===== Absensi Umum =====
    // routes/web.php
    Route::get('/absensi/data', [AbsensiController::class, 'getData'])->name('absensi.data');

    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');               // Lihat absensi
    Route::post('/absensi/masuk', [AbsensiController::class, 'masuk'])->name('absensi.masuk');      // Absen keluar

    // Scan kamera (via laptop/pc)
    Route::get('/scan-kamera', [AbsensiController::class, 'showScanKamera'])->name('absensi.scan.kamera');

    // Izin
    Route::get('/izin/ajukan', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin/ajukan', [IzinController::class, 'store'])->name('izin.store');
    Route::get('/izin/saya', [IzinController::class, 'myIzin'])->name('izin.my');
    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::post('/izin/{izin}/approve', [IzinController::class, 'changeStatus'])->name('izin.changeStatus');
    Route::get('/izin/{izin}/edit', [IzinController::class, 'edit'])->name('izin.edit');
    Route::delete('/izin/{izin}', [IzinController::class, 'destroy'])->name('izin.destroy');
    Route::put('/izin/{izin}', [IzinController::class, 'update'])->name('izin.update');


    // ========== Admin & Superadmin Only ==========
    Route::middleware(['role:admin,superadmin'])->group(function () {

        // Routes untuk manajemen pengguna
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/admin/users/{id}/restore', [UserController::class, 'restore'])->name('admin.users.restore');

        // Generate QR code
        Route::get('/absensi/generate-qr', [AbsensiController::class, 'generateQr'])->name('absensi.generate.qr');

        // Endpoint khusus AJAX untuk refresh QR & daftar absensi
        Route::get('/absensi/qr-refresh', [AbsensiController::class, 'refreshQr'])->name('absensi.qr.refresh');

        // Edit dan update data absen
        Route::get('/absensi/{id}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('/absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');

        // Hapus data absen
        Route::get('/absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::delete('/absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');

        // CRUD Jadwal Guru
        Route::get('admin/jadwal', [JadwalController::class, 'index'])->name('admin.jadwal.index'); // Menampilkan daftar jadwal guru
        Route::post('admin/jadwal', [JadwalController::class, 'storeJadwal'])->name('admin.jadwal.store'); // Menyimpan jadwal guru baru
        Route::get('admin/jadwal/{id}/edit', [JadwalController::class, 'editJadwal'])->name('admin.jadwal.edit'); // Menampilkan form edit jadwal
        Route::put('admin/jadwal/{id}', [JadwalController::class, 'updateJadwal'])->name('admin.jadwal.update'); // Memperbarui jadwal
        Route::delete('admin/jadwal/{id}', [JadwalController::class, 'destroyJadwal'])->name('admin.jadwal.destroy'); // Menghapus jadwal
        Route::get('/admin/jadwal/guru-available', [JadwalController::class, 'getAvailableGuru']);
        // Route untuk mendapatkan guru yang tersedia pada tahun ajaran tertentu
        Route::get('/admin/jadwal/guru-available', [JadwalController::class, 'getAvailableGuru']);
        Route::post('/admin/tahunajaran/{id}/aktifkan', [JadwalController::class, 'aktifkan'])->name('admin.tahunajaran.aktifkan');
        Route::delete('/admin/jadwal/{id}', [JadwalController::class, 'destroyJadwal'])->name('admin.jadwal.destroy');

        // CRUD Tahun Ajaran
        Route::post('admin/tahunajaran', [JadwalController::class, 'storeTahunAjaran'])->name('admin.tahunajaran.store'); // Menyimpan tahun ajaran baru
        Route::delete('admin/tahunajaran/{id}', [JadwalController::class, 'destroyTahunAjaran'])->name('admin.tahunajaran.destroy'); // Menghapus tahun ajaran
        Route::get('admin/tahunajaran/{id}/edit', [JadwalController::class, 'editTahunAjaran'])->name('admin.tahunajaran.edit'); // Menampilkan form edit tahun ajaran
        Route::put('admin/tahunajaran/{id}', [JadwalController::class, 'updateTahunAjaran'])->name('admin.tahunajaran.update'); // Memperbarui tahun ajaran

        // Izin
        Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
        Route::post('/izin/{izin}/status', [IzinController::class, 'updateStatus'])->name('izin.status');

        Route::get('/laporan/bulanan', [LaporanController::class, 'laporanBulanan'])->name('laporan.bulanan');

        // Cetak PDF laporan bulanan
        Route::get('/laporan/bulanan/pdf', [LaporanController::class, 'rekapPdf'])->name('laporan.rekap.pdf');

        // Export Excel

        Route::get('/laporan/rekap-excel', [LaporanController::class, 'rekapExcel'])->name('laporan.rekap.excel');

        // Routes untuk manajemen hari libur
        Route::get('/admin/libur', [LiburController::class, 'index'])->name('admin.libur.index');
        Route::get('/admin/libur/create', [LiburController::class, 'create'])->name('admin.libur.create');
        Route::post('/admin/libur', [LiburController::class, 'store'])->name('admin.libur.store');
        Route::get('/admin/libur/{id}/edit', [LiburController::class, 'edit'])->name('admin.libur.edit');
        Route::put('/admin/libur/{id}', [LiburController::class, 'update'])->name('admin.libur.update');
        Route::delete('/admin/libur/{id}', [LiburController::class, 'destroy'])->name('admin.libur.destroy');

        // Routes untuk log aktivitas
        Route::get('/admin/logs', [LogController::class, 'index'])->name('admin.logs.index');
    });
    // ========== Superadmin Only ==========
    Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::resource('admins', AdminController::class);
    });




});

// =========================
// Auth Routes dari Breeze/Fortify/Jetstream
// =========================
require __DIR__.'/auth.php';
