<?php

use App\Http\Controllers\AnimalProductionController;
use App\Http\Controllers\AvocadoCropController;
use App\Http\Controllers\CattleController;
use App\Http\Controllers\CoffeCropController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HenController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\UserAppController;
use App\Http\Controllers\UserController;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

 Route::get('user_apps', [UserAppController::class,'index'])->name('api.v1.userapps.index');
 Route::post('user_apps', [UserAppController::class,'store'])->name('api.v1.userapps.store');
 Route::get('user_apps/{user_app}', [UserAppController::class,'show'])->name('api.v1.userapps.show');

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

 Route::get('crops', [CropController::class,'index'])->name('api.v1.crops.index');
 Route::post('crops', [CropController::class,'store'])->name('api.v1.crops.store');
 Route::get('crops/{crop}', [CropController::class,'show'])->name('api.v1.crops.show');

 Route::get('finances', [FinanceController::class,'index'])->name('api.v1.finances.index');
 Route::post('finances', [FinanceController::class,'store'])->name('api.v1.finances.store');
 Route::get('finances/{finance}', [FinanceController::class,'show'])->name('api.v1.finances.show');

  Route::get('coffe_crops', [CoffeCropController::class,'index'])->name('api.v1.coffe_crops.index');
 Route::post('coffe_crops', [CoffeCropController::class,'store'])->name('api.v1.coffe_crops.store');
 Route::get('coffe_crops/{coffe_crop}', [CoffeCropController::class,'show'])->name('api.v1.coffe_crops.show');

 
 Route::get('avocado_crops', [AvocadoCropController::class,'index'])->name('api.v1.avocado_crops.index');
 Route::post('avocado_crops', [AvocadoCropController::class,'store'])->name('api.v1.avocado_crops.store');
 Route::get('avocado_crops/{avocado_crop}', [AvocadoCropController::class,'show'])->name('api.v1.avocado_crops.show');