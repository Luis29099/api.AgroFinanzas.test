<?php

use App\Http\Controllers\AnimalProductionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvocadoCropController;
use App\Http\Controllers\CattleController;
use App\Http\Controllers\CoffeCropController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HenController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\AdminFinancesController;
use App\Http\Controllers\AdminCommentsController;

// ── USUARIOS ──────────────────────────────────────────────────
Route::get('user_apps',            [UserController::class, 'index'])->name('api.v1.users.index');
Route::post('user_apps',           [UserController::class, 'store'])->name('api.v1.users.store');
Route::get('user_apps/{user_app}', [UserController::class, 'show'])->name('api.v1.users.show');

// ── AUTH ──────────────────────────────────────────────────────
Route::post('/register',                        [AuthController::class, 'register']);
Route::post('/login',                           [AuthController::class, 'login']);
Route::post('/verify-code',                     [AuthController::class, 'verifyCode']);
Route::post('/resend-code',                     [AuthController::class, 'resendCode']);
Route::post('/users/{id}/update-profile',       [AuthController::class, 'updateProfile']);
Route::post('/users/{id}/send-delete-code',     [AuthController::class, 'sendDeleteCode']);
Route::delete('/users/{id}',                    [AuthController::class, 'deleteAccount']);

// ── RECOMENDACIONES ───────────────────────────────────────────
Route::get('recommendations',                  [RecommendationController::class, 'index']);
Route::post('recommendations',                 [RecommendationController::class, 'store']);
Route::get('recommendations/{recommendation}', [RecommendationController::class, 'show']);

// ── NOTIFICACIONES ────────────────────────────────────────────
Route::get('/notifications/{userId}',              [NotificationController::class, 'index']);
Route::get('/notifications/{userId}/unread-count', [NotificationController::class, 'unreadCount']);
Route::patch('/notifications/{id}/read',           [NotificationController::class, 'markRead']);
Route::patch('/notifications/{userId}/read-all',   [NotificationController::class, 'markAllRead']);

// ── PRODUCCIÓN ANIMAL ─────────────────────────────────────────
Route::get('animal_productions',                     [AnimalProductionController::class, 'index'])->name('api.v1.animal_production.index');
Route::post('animal_productions',                    [AnimalProductionController::class, 'store'])->name('api.v1.animal_production.store');
Route::get('animal_productions/{animal_production}', [AnimalProductionController::class, 'show'])->name('api.v1.animal_production.show');

// ── GALLINAS ──────────────────────────────────────────────────
Route::get('hens',        [HenController::class, 'index'])->name('api.v1.hen.index');
Route::post('hens',       [HenController::class, 'store'])->name('api.v1.hen.store');
Route::get('hens/{hen}',  [HenController::class, 'show'])->name('api.v1.hen.show');

// ✅ GANADO — Control individual por animal ────────────────────
Route::get('cattles',                  [CattleController::class, 'index']);
Route::post('cattles',                 [CattleController::class, 'store']);
Route::get('cattles/{id}',             [CattleController::class, 'show']);
Route::post('cattles/{id}',            [CattleController::class, 'update']);   // POST porque lleva foto (multipart)
Route::delete('cattles/{id}',          [CattleController::class, 'destroy']);
Route::post('cattles/{id}/birth',      [CattleController::class, 'registerBirth']); // ← Registrar nacimiento

// ── CULTIVOS ──────────────────────────────────────────────────
Route::get('crops',         [CropController::class, 'index'])->name('api.v1.crops.index');
Route::post('crops',        [CropController::class, 'store'])->name('api.v1.crops.store');
Route::get('crops/{crop}',  [CropController::class, 'show'])->name('api.v1.crops.show');

// ── CAFÉ ──────────────────────────────────────────────────────
Route::get('coffe_crops',               [CoffeCropController::class, 'index'])->name('api.v1.coffe_crops.index');
Route::post('coffe_crops',              [CoffeCropController::class, 'store'])->name('api.v1.coffe_crops.store');
Route::get('coffe_crops/{coffe_crop}',  [CoffeCropController::class, 'show'])->name('api.v1.coffe_crops.show');

// ── AGUACATE ──────────────────────────────────────────────────
Route::get('avocado_crops',                 [AvocadoCropController::class, 'index'])->name('api.v1.avocado_crops.index');
Route::post('avocado_crops',                [AvocadoCropController::class, 'store'])->name('api.v1.avocado_crops.store');
Route::get('avocado_crops/{avocado_crop}',  [AvocadoCropController::class, 'show'])->name('api.v1.avocado_crops.show');

// ── FINANZAS ──────────────────────────────────────────────────
Route::prefix('finances')->group(function () {
    Route::get('/',                       [FinanceController::class, 'index']);
    Route::post('/',                      [FinanceController::class, 'store']);
    Route::get('/{id}',                   [FinanceController::class, 'show']);
    Route::put('/{id}',                   [FinanceController::class, 'update']);
    Route::delete('/{id}',                [FinanceController::class, 'destroy']);
    Route::get('/statistics/summary',     [FinanceController::class, 'statistics']);
    Route::patch('/{id}/pay-installment', [FinanceController::class, 'payDebtInstallment']);
    Route::post('/ajax',                  [FinanceController::class, 'storeAjax']);
});

// ── LOGIN (sin middleware) ────────────────────────────────────
Route::post('/admin/login',  [AdminAuthController::class, 'login']);

// ── RUTAS PROTEGIDAS (requieren token de admin) ───────────────
Route::middleware(['admin.token'])->prefix('admin')->group(function () {

    // Auth
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    Route::get('/me',      [AdminAuthController::class, 'me']);

    // Usuarios
    Route::get('/users',                [AdminUsersController::class, 'index']);
    Route::get('/users/{id}',           [AdminUsersController::class, 'show']);
    Route::patch('/users/{id}/toggle',  [AdminUsersController::class, 'toggle']);
    Route::delete('/users/{id}',        [AdminUsersController::class, 'destroy']);

    // Finanzas
    Route::get('/finances',                    [AdminFinancesController::class, 'index']);
    Route::get('/finances/user/{userId}',      [AdminFinancesController::class, 'byUser']);

    // Comentarios
    Route::get('/comments',                    [AdminCommentsController::class, 'index']);
    Route::get('/comments/user/{userId}',      [AdminCommentsController::class, 'byUser']);
    Route::delete('/comments/{id}',            [AdminCommentsController::class, 'destroy']);
});