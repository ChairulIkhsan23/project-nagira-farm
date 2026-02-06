<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\KategoriProdukController;
use App\Http\Controllers\Admin\TernakController;
use App\Http\Controllers\Admin\BreedingController;
use App\Http\Controllers\Admin\ArtikelController;
use App\Http\Controllers\Admin\GaleriMediaController;
use App\Http\Controllers\Admin\PengaduanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TernakExportController;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Profil User
Route::prefix('profil')->name('profil.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
});

// Produk Manajemen
Route::prefix('produk')->name('produk.')->group(function () {
    Route::get('/', [ProdukController::class, 'index'])->name('index');
    Route::get('/create', [ProdukController::class, 'create'])->name('create');
    Route::post('/', [ProdukController::class, 'store'])->name('store');
    Route::get('/{produk}', [ProdukController::class, 'show'])->name('show');
    Route::get('/{produk}/edit', [ProdukController::class, 'edit'])->name('edit');
    Route::put('/{produk}', [ProdukController::class, 'update'])->name('update');
    Route::delete('/{produk}', [ProdukController::class, 'destroy'])->name('destroy');
});

// Kategori Produk
Route::prefix('kategori-produk')->name('kategori-produk.')->group(function () {
    Route::get('/', [KategoriProdukController::class, 'index'])->name('index');
    Route::get('/create', [KategoriProdukController::class, 'create'])->name('create');
    Route::post('/', [KategoriProdukController::class, 'store'])->name('store');
    Route::get('/{kategori_produk}', [KategoriProdukController::class, 'show'])->name('show');
    Route::get('/{kategori_produk}/edit', [KategoriProdukController::class, 'edit'])->name('edit');
    Route::put('/{kategori_produk}', [KategoriProdukController::class, 'update'])->name('update');
    Route::delete('/{kategori_produk}', [KategoriProdukController::class, 'destroy'])->name('destroy');
});

// Ternak 
Route::prefix('ternak')->name('ternak.')->group(function () {
    // Special Menu Views
    Route::get('/breeding', [TernakController::class, 'breedingIndex'])->name('breeding.index');
    Route::get('/fattening', [TernakController::class, 'fattening'])->name('fattening');
    
    Route::get('/', [TernakController::class, 'index'])->name('index');
    Route::get('/create', [TernakController::class, 'create'])->name('create');
    Route::post('/', [TernakController::class, 'store'])->name('store');
    Route::get('/{ternak}', [TernakController::class, 'show'])->name('show');
    Route::get('/{ternak}/edit', [TernakController::class, 'edit'])->name('edit');
    Route::put('/{ternak}', [TernakController::class, 'update'])->name('update');
    Route::delete('/{ternak}', [TernakController::class, 'destroy'])->name('destroy');
    
    // Fungsi Export Pdf/Excel
    Route::get('/export/pdf', [TernakExportController::class, 'exportPDF'])->name('export.pdf');
    Route::get('/export/excel', [TernakExportController::class, 'exportExcel'])->name('export.excel');
    
    // Breeding Manajemen 
    Route::prefix('breeding')->name('breeding.')->group(function () {
        Route::get('/create', [BreedingController::class, 'create'])->name('create');
        Route::post('/', [BreedingController::class, 'store'])->name('store');
        Route::get('/{breeding}/edit', [BreedingController::class, 'edit'])->name('edit');
        Route::put('/{breeding}', [BreedingController::class, 'update'])->name('update');
        Route::delete('/{breeding}', [BreedingController::class, 'destroy'])->name('destroy');
    });
});

// Artikel Manajemen
Route::prefix('artikel')->name('artikel.')->group(function () {
    Route::get('/', [ArtikelController::class, 'index'])->name('index');
    Route::get('/create', [ArtikelController::class, 'create'])->name('create');
    Route::post('/', [ArtikelController::class, 'store'])->name('store');
    Route::get('/{artikel}', [ArtikelController::class, 'show'])->name('show');
    Route::get('/{artikel}/edit', [ArtikelController::class, 'edit'])->name('edit');
    Route::put('/{artikel}', [ArtikelController::class, 'update'])->name('update');
    Route::delete('/{artikel}', [ArtikelController::class, 'destroy'])->name('destroy');
});

// Galeri Media Manajemen
Route::prefix('galeri-media')->name('galeri-media.')->group(function () {
    Route::get('/', [GaleriMediaController::class, 'index'])->name('index');
    Route::get('/create', [GaleriMediaController::class, 'create'])->name('create');
    Route::post('/', [GaleriMediaController::class, 'store'])->name('store');
    Route::get('/{galeri_media}', [GaleriMediaController::class, 'show'])->name('show');
    Route::get('/{galeri_media}/edit', [GaleriMediaController::class, 'edit'])->name('edit');
    Route::put('/{galeri_media}', [GaleriMediaController::class, 'update'])->name('update');
    Route::delete('/{galeri_media}', [GaleriMediaController::class, 'destroy'])->name('destroy');
});

// Pengaduan Manajemen
Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
    Route::get('/', [PengaduanController::class, 'index'])->name('index');
    Route::get('/{pengaduan}', [PengaduanController::class, 'show'])->name('show');
    Route::put('/{pengaduan}', [PengaduanController::class, 'update'])->name('update');
});


// Support/Help page (jika dibutuhkan)
Route::get('/support', function () {
    return inertia('Admin/Support/Index');
})->name('support');

// Fungsi Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');