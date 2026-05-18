<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\StudioController;
use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Cashier\BookingController;

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
    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('/', 'index')->name('profile.index');
        Route::put('/', 'update')->name('profile.update');
    });

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        // Cashiers Route
        Route::get('/cashiers/data', [CashierController::class, 'data'])->name('cashiers.data');
        Route::resource('cashiers', CashierController::class);

        // Movies Route
        Route::resource('movies', MovieController::class);

        // Studios Route
        Route::resource('studios', StudioController::class);
        Route::post('/studios/render-seats', [StudioController::class, 'renderSeats'])->name('studios.render');
        Route::post('/studios/{studio}/add-vip', [StudioController::class, 'addVip'])->name('studios.add-vip');

        // Schedule Route
        Route::resource('schedules', ScheduleController::class);
    });

    // Cashier Routes
    Route::middleware('role:cashier')->group(function () {
        // Booking Route
        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
        });
    });

    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
