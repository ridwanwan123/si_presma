<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MadrasahController;

Route::prefix('v1')
    ->middleware('apikey')
    ->group(function () {

        Route::prefix('madrasahs')->group(function () {

            Route::get('/', [MadrasahController::class, 'index']);
            Route::get('/{id}', [MadrasahController::class, 'show']);

        });

    });