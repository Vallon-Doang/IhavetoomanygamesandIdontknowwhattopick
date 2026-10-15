<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PickerController;
use App\Http\Controllers\SteamController;
use App\Http\Controllers\SteamResetRequestController;
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

    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
    Route::post('/community/posts', [CommunityController::class, 'store'])->name('community.posts.store');
    Route::get('/community/posts/{post}/edit', [CommunityController::class, 'edit'])->name('community.posts.edit');
    Route::put('/community/posts/{post}', [CommunityController::class, 'update'])->name('community.posts.update');
    Route::delete('/community/posts/{post}', [CommunityController::class, 'destroy'])->name('community.posts.destroy');

    Route::get('/steam/connect', [SteamController::class, 'connect'])->name('steam.connect');
    Route::get('/steam/callback', [SteamController::class, 'callback'])->name('steam.callback');
    Route::post('/steam/sync', [SteamController::class, 'sync'])->middleware('throttle:6,1')->name('steam.sync');
    Route::delete('/steam/disconnect', [SteamController::class, 'disconnect'])->name('steam.disconnect');
    Route::post('/steam/reset-request', [SteamResetRequestController::class, 'store'])->middleware('throttle:10,1')->name('steam.reset-request');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}/steam', [Admin\UserController::class, 'unlinkSteam'])->name('users.steam.destroy');
    Route::get('/games', [Admin\GameController::class, 'index'])->name('games.index');
    Route::get('/syncs', [Admin\SyncController::class, 'index'])->name('syncs.index');

    Route::get('/posts', [Admin\PostController::class, 'index'])->name('posts.index');
    Route::patch('/posts/{post}/moderation', [Admin\PostController::class, 'moderate'])->name('posts.moderate');
    Route::delete('/posts/{post}', [Admin\PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/announcements', [Admin\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [Admin\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [Admin\AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [Admin\AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [Admin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::get('/analytics', [Admin\AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/steam-requests', [Admin\SteamResetRequestController::class, 'index'])->name('steam-requests.index');
    Route::patch('/steam-requests/{steamResetRequest}', [Admin\SteamResetRequestController::class, 'update'])->name('steam-requests.update');
});