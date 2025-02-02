<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HostController;

Route::get('/', [HostController::class, 'index']);
