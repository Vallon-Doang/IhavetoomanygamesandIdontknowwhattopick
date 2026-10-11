<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PickerController;
use App\Http\Controllers\SteamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
    Route::patch('/library/{userGame}/exclusion', [LibraryController::class, 'toggleExclusion'])->name('library.exclusion');

    Route::get('/picker', [PickerController::class, 'index'])->name('picker.index');
    Route::post('/picker/spin', [PickerController::class, 'spin'])->name('picker.spin');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

    Route::get('/steam/connect', [SteamController::class, 'connect'])->name('steam.connect');
    Route::get('/steam/callback', [SteamController::class, 'callback'])->name('steam.callback');
    Route::post('/steam/sync', [SteamController::class, 'sync'])->middleware('throttle:6,1')->name('steam.sync');
    Route::delete('/steam/disconnect', [SteamController::class, 'disconnect'])->name('steam.disconnect');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/games', [Admin\GameController::class, 'index'])->name('games.index');
    Route::get('/syncs', [Admin\SyncController::class, 'index'])->name('syncs.index');
});