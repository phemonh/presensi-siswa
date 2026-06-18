<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\WaliKelasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Presensi (QR Code) - Public route for scanning
Route::get('/presensi/scan', [PresensiController::class, 'showQRCode'])->name('presensi.scan');
Route::post('/presensi/process', [PresensiController::class, 'processScan'])->name('presensi.process');

// Student routes
Route::middleware(['auth'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [PresensiController::class, 'history'])->name('dashboard');
    Route::get('/history', [PresensiController::class, 'history'])->name('history');
});

// Wali Kelas routes
Route::middleware(['auth', 'role:wali_kelas'])->prefix('wali')->name('wali.')->group(function () {
    Route::get('/dashboard', [WaliKelasController::class, 'dashboard'])->name('dashboard');
    Route::get('/detail', [WaliKelasController::class, 'detail'])->name('detail');
    Route::get('/export', [WaliKelasController::class, 'export'])->name('export');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Students management
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::get('/students/create', [AdminController::class, 'createStudent'])->name('students.create');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('students.store');
    
    // Classes management
    Route::get('/classes', [AdminController::class, 'classes'])->name('classes');
    Route::get('/classes/create', [AdminController::class, 'createClass'])->name('classes.create');
    Route::post('/classes', [AdminController::class, 'storeClass'])->name('classes.store');
    
    // Teachers management
    Route::get('/teachers', [AdminController::class, 'teachers'])->name('teachers');
    Route::get('/teachers/create', [AdminController::class, 'createTeacher'])->name('teachers.create');
    Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('teachers.store');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
});
