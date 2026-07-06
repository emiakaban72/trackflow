<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\AdminOnly;
use App\Http\Controllers\CountryController;

// ==========================================
// RUTE AUTENTIKASI (PUBLIK)
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// RUTE HALAMAN UTAMA / DASHBOARD DEPAN (PUBLIK)
// ==========================================

// GAS! Arahkan langsung ke CountryController agar semua API otomatis jalan
Route::get('/', [CountryController::class, 'index']); 

// Endpoint untuk mengambil data detail negara secara real-time via AJAX
Route::get('/country-detail', [DashboardController::class, 'getCountryDetail']);

// ==========================================
// RUTE KHUSUS ADMIN (DIGEMBOK MIDDLEWARE AUTH & PREFIX /admin)
// ==========================================
Route::middleware(['auth', PreventBackHistory::class, AdminOnly::class])->prefix('admin')->group(function () {    
    
    // Rute Dasbor Admin (/admin/dashboard)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Rute Manajemen Pengguna
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store'); 
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Rute Manajemen Pelabuhan
    Route::get('/ports', [AdminController::class, 'ports'])->name('admin.ports');
    Route::post('/ports', [AdminController::class, 'storePort'])->name('admin.ports.store');
    Route::put('/ports/{id}', [AdminController::class, 'updatePort'])->name('admin.ports.update');
    Route::delete('/ports/{id}', [AdminController::class, 'deletePort'])->name('admin.ports.delete');

    // Rute Manajemen Artikel
    Route::get('/articles', [AdminController::class, 'articles'])->name('admin.articles');
    Route::post('/articles', [AdminController::class, 'storeArticle'])->name('admin.articles.store');
    Route::put('/articles/{id}', [AdminController::class, 'updateArticle'])->name('admin.articles.update');
    Route::delete('/articles/{id}', [AdminController::class, 'deleteArticle'])->name('admin.articles.delete');
    
});

// Rute Pencarian Negara (dari teman)
Route::get('/country', [CountryController::class, 'index'])->name('country.index');
Route::post('/country', [CountryController::class, 'search'])->name('country.search');