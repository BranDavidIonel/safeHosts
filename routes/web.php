<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HostController;
use App\Http\Controllers\UserCustomListController;

Route::get('/', [HostController::class, 'index']);
//custom user list
Route::get('/custom-users-list', [UserCustomListController::class, 'index'])->name('custom-list.index');
Route::post('/custom-users-list', [UserCustomListController::class, 'store'])->name('custom-list.store');
Route::delete('/custom-users-list/{id}', [UserCustomListController::class, 'destroy'])->name('custom-list.destroy');
