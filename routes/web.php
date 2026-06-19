<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MadrasahController;
use App\Http\Controllers\ActivityController;
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
    

    Route::get('/activity', [ActivityController::class,'index'])
    ->name('activity.index');
});