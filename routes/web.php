<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\SettingController;

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
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Profile Routes
    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('/', 'index')->name('profile.index');
        Route::put('/{user}', 'update')->name('profile.update');
    });

    // Password Routes
    Route::controller(PasswordController::class)->prefix('password')->group(function () {
        Route::get('/', 'edit')->name('password.edit');
        Route::put('/', 'update')->name('password.update');
        Route::post('/reset', 'reset')->name('password.reset');
    });

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        // Cashiers Routes
        Route::get('/cashiers/data', [CashierController::class, 'data'])->name('cashiers.data');
        Route::resource('cashiers', CashierController::class)->except('edit');

        // Movies Routes
        Route::resource('movies', MovieController::class)->except(['index', 'show']);

        // Studios Routes
        Route::resource('studios', StudioController::class)->except(['index', 'show']);
        Route::post('/studios/render-seats', [StudioController::class, 'renderSeats'])->name('studios.render');
        Route::post('/studios/{studio}/add-vip', [StudioController::class, 'addVip'])->name('studios.add-vip');

        // Schedule Routes
        Route::resource('schedules', ScheduleController::class);

        // Settings Routes
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Cashier Routes
    Route::middleware('role:cashier')->group(function () {
        // Booking Routes
        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::get('/{schedule}', [BookingController::class, 'show'])->name('show');
        });
    });

    Route::middleware('role:admin,cashier')->group(function () {

        Route::get('movies', [MovieController::class, 'index'])->name('movies.index');

        Route::get('movies/{movie}', [MovieController::class, 'show'])->name('movies.show');

        Route::get('studios', [StudioController::class, 'index'])->name('studios.index');

        Route::get('studios/{studio}', [StudioController::class, 'show'])->name('studios.show');
    });

    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
