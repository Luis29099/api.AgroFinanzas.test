<?php

use App\Http\Controllers\UserAppController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('user_app', [UserAppController::class, 'index'])->name('api.v1.user_app.index');
Route::post('user_app', [UserAppController::class, 'store'])->name('api.v1.user_app.store');
Route::get('user_app/{user_app}', [UserAppController::class, 'store'])->name('api.v1.user_app.show');

