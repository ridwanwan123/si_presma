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
        /*
        |--------------------------------------------------------------------------
        | SECTION IMPORT
        |--------------------------------------------------------------------------
        */

        Route::get('{jenis}/import', [PrestasiController::class, 'import'])
            ->name('import');

        Route::post('{jenis}/import', [PrestasiController::class, 'upload'])
            ->name('import.upload');

        Route::post('{jenis}/checking_import',
            [PrestasiController::class, 'checking_import_prestasi']
        )
            ->name('checking_import');

        Route::post('{jenis}/save-preview',
            [PrestasiController::class,'save_preview']
        )
            ->name('save_preview');

        Route::get('{jenis}/preview',
            [PrestasiController::class,'preview']
        )
            ->name('preview');

        Route::post('{jenis}/store-import',
            [PrestasiController::class,'store_import']
        )
            ->name('store_import');

        Route::get('{jenis}/template',
            [PrestasiController::class,'template']
        )
            ->name('template');

        /*
        |--------------------------------------------------------------------------
        | CRUD PRESTASI
        |--------------------------------------------------------------------------
        */

        Route::get('{jenis}/create',
            [PrestasiController::class,'create']
        )
            ->name('create');


        Route::post('{jenis}',
            [PrestasiController::class,'store']
        )
            ->name('store');


        Route::get('{jenis}/{id}/edit',
            [PrestasiController::class,'edit']
        )
            ->name('edit');


        Route::put('{jenis}/{id}',
            [PrestasiController::class,'update']
        )
            ->name('update');


        Route::delete('{jenis}/{id}',
            [PrestasiController::class,'destroy']
        )
            ->name('destroy');

        /*
        |--------------------------------------------------------------------------
        | DATATABLE SERVER SIDE
        |--------------------------------------------------------------------------
        */

        Route::get('{jenis}/data',
            [PrestasiController::class,'data']
        )
            ->where('jenis', 'akademik|non-akademik|keagamaan|gtk|lembaga')
            ->name('data');



        /*
        |--------------------------------------------------------------------------
        | INDEX (PALING BAWAH)
        |--------------------------------------------------------------------------
        */

        Route::get('{jenis}',
            [PrestasiController::class,'index']
        )
            ->where('jenis', 'akademik|non-akademik|keagamaan|gtk|lembaga')
            ->name('index');

    });
    /*
    |--------------------------------------------------------------------------
    | SECTION Bidang Prestasi
    |--------------------------------------------------------------------------
    */
});