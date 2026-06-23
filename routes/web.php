<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MadrasahController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\PrestasiController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login.form');
});

/*
|--------------------------------------------------------------------------
| GUEST
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login.form');

    Route::post('/login', [LoginController::class, 'authenticate'])
        ->name('login');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | LOGIN CONTROLLER
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    Route::get('/ubah-password', [LoginController::class, 'changePassword'])
        ->name('ubah-password');

    Route::post('/ubah-password', [LoginController::class, 'updatePassword'])
        ->name('ubah-password.update');
    /*
    |--------------------------------------------------------------------------
    | END LOGIN CONTROLLER
    |--------------------------------------------------------------------------
    */
    
    /*
    | Dashboard (ALL ROLE)
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    
    Route::resource('madrasah', MadrasahController::class);
    
    Route::resource('activity', ActivityController::class);


    /*
    |--------------------------------------------------------------------------
    | SECTION Bidang Prestasi
    |--------------------------------------------------------------------------
    */
    Route::prefix('prestasi')->name('prestasi.')->group(function () {

        Route::get('{jenis}/import', [PrestasiController::class, 'import'])
            ->name('import');

        Route::post('{jenis}/import', [PrestasiController::class, 'upload'])
            ->name('import.upload');

        Route::post('{jenis}/checking_import',[PrestasiController::class, 'checking_import_prestasi'])
            ->name('checking_import');
        
        Route::post('{jenis}/save-preview',[PrestasiController::class,'save_preview']) //tombol preview
            ->name('save_preview');

        Route::get('{jenis}/preview',[PrestasiController::class,'preview']) // bawa data ke halaman preview
            ->name('preview');

        Route::post('{jenis}/store-import', [PrestasiController::class, 'store_import']) // simpan data ke database
            ->name('store_import');

        Route::get('{jenis}/template', [PrestasiController::class, 'template'])
            ->name('template');

        Route::get('{jenis}', [PrestasiController::class, 'index'])
            ->where('jenis', 'akademik|non-akademik|keagamaan|gtk|lembaga')
            ->name('index');

        Route::get('{jenis}/data', [PrestasiController::class, 'data'])
            ->where('jenis', 'akademik|non-akademik|keagamaan|gtk|lembaga')
            ->name('data');
    });
    /*
    |--------------------------------------------------------------------------
    | SECTION Bidang Prestasi
    |--------------------------------------------------------------------------
    */
});