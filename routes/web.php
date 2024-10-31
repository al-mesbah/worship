<?php

use Illuminate\Support\Facades\Route;
Route::group(['prefix' => 'login', 'as' => 'login'], function () {
    Route::get('/', [\App\Http\Controllers\AuthenticationController::class, 'index']);
    Route::get('/login-with-app', [\App\Http\Controllers\AuthenticationController::class, 'with_app'])->name('with-app');
    Route::get('/captcha', function () {
        return [
            'url' => captcha_src()
        ];
    })->name('.captcha');
    Route::get('/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
    });
    Route::post('/', [\App\Http\Controllers\AuthenticationController::class, 'login']);
    Route::put('/', [\App\Http\Controllers\AuthenticationController::class, 'check_code']);
});

Route::middleware('auth')->group(function () {
    Route::post('/reservation', [\App\Http\Controllers\ReservationController::class, 'get'])->name('reservation');
    Route::put('/reservation', [\App\Http\Controllers\ReservationController::class, 'reserved']);
    Route::get('{any?}', function () {
        return view('welcome');
    })->name('vue');
});

