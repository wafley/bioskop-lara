<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\StudioController;
use App\Http\Controllers\Admin\OperatorController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Profile Route
    Route::controller(\App\Http\Controllers\ProfileController::class)->prefix('profile')->group(function () {
        Route::get('/', 'index')->name('profile.index');
        Route::put('/', 'update')->name('profile.update');
    });

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        // Operators Route
        Route::get('/operators/data', [OperatorController::class, 'data'])->name('operators.data');
        Route::resource('operators', OperatorController::class);

        // Movies Route
        Route::get('/movies/data', [MovieController::class, 'data'])->name('movies.data');
        Route::resource('movies', MovieController::class);

        // Studios Route
        Route::resource('studios', StudioController::class);
        Route::post('/studios/render-seats', [StudioController::class, 'renderSeats'])->name('studios.render');
        Route::post('/studios/{studio}/add-vip', [StudioController::class, 'addVip'])->name('studios.add-vip');
    });

    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
