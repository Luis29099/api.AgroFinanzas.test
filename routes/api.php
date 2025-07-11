<?php

use App\Http\Controllers\AnimalProductionController;
use App\Http\Controllers\CattleController;
use App\Http\Controllers\HenController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\UserAppController;
use App\Http\Controllers\UserController;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

 Route::get('user_apps', [UserAppController::class,'index'])->name('api.v1.user_apps.index');
 Route::post('user_apps', [UserAppController::class,'store'])->name('api.v1.user_apps.store');
 Route::get('user_apps/{user_app}', [UserAppController::class,'show'])->name('api.v1.user_apps.show');

Route::get('recomendations', [RecommendationController::class,'index'])->name('api.v1.recomendation.index');
 Route::post('recomendations', [RecommendationController::class,'store'])->name('api.v1.recomendation.store');
 Route::get('recomendations/{recomendation}', [RecommendationController::class,'show'])->name('api.v1.recomendation.show');

 Route::get('animal_productions', [AnimalProductionController::class,'index'])->name('api.v1.animal_production.index');
 Route::post('animal_productions', [AnimalProductionController::class,'store'])->name('api.v1.animal_production.store');
 Route::get('animal_productions/{animal_production}', [AnimalProductionController::class,'show'])->name('api.v1.animal_production.show');

 Route::get('hens', [HenController::class,'index'])->name('api.v1.hen.index');
 Route::post('hens', [HenController::class,'store'])->name('api.v1.hen.store');
 Route::get('hens/{hen}', [HenController::class,'show'])->name('api.v1.hen.show');

  Route::get('cattles', [CattleController::class,'index'])->name('api.v1.cattle.index');
 Route::post('cattles', [CattleController::class,'store'])->name('api.v1.cattle.store');
 Route::get('cattles/{cattle}', [CattleController::class,'show'])->name('api.v1.cattle.show');