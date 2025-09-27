<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HostController;
use App\Http\Controllers\UserCustomListController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HostsDownloadController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [HostController::class, 'index']);

Route::post('/generate-hosts-guest', [HostsDownloadController::class, 'generateGuest'])->name('generate.hosts.guest');
Route::post('/download-hosts-guest', [HostsDownloadController::class, 'downloadGuest'])->name('download.hosts.guest');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [HostController::class, 'admin'])->name('admin.dashboard');
    Route::get('/download-hosts-admin', [HostsDownloadController::class, 'downloadAdmin'])->name('download.hosts.admin');
    //custom user list
    Route::get('/custom-users-list', [UserCustomListController::class, 'index'])->name('custom-list.index');
    Route::post('/custom-users-list', [UserCustomListController::class, 'store'])->name('custom-list.store');
    Route::delete('/custom-users-list/{id}', [UserCustomListController::class, 'destroy'])->name('custom-list.destroy');
});


