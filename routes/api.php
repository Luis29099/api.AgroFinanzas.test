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
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\FinanceExportController;
use App\Http\Controllers\ClimaController;
use App\Http\Controllers\PreciosController;
use Illuminate\Http\Request;

// ── AUTH ──────────────────────────────────────────────────────
Route::post('/register',        [AuthController::class, 'register']);
Route::post('/login',           [AuthController::class, 'login']);
Route::post('/verify-code',     [AuthController::class, 'verifyCode']);
Route::post('/resend-code',     [AuthController::class, 'resendCode']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

// ── PÚBLICAS ──────────────────────────────────────────────────
Route::get('/clima',   [ClimaController::class, 'getClima']);
Route::get('precios',  [PreciosController::class, 'index']);

// ── PROTECTED USER ROUTES ─────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout',                    [AuthController::class, 'logout']);
    Route::post('/users/{id}/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/users/{id}/send-delete-code',[AuthController::class, 'sendDeleteCode']);
    Route::delete('/users/{id}',              [AuthController::class, 'deleteAccount']);
    Route::post('/users/{id}/delete-confirm', [AuthController::class, 'deleteAccount']);

    // ── USUARIOS ──────────────────────────────────────────────
    Route::get('user_apps',            [UserController::class, 'index']);
    Route::post('user_apps',           [UserController::class, 'store']);
    Route::get('user_apps/{user_app}', [UserController::class, 'show']);

    // ── FINANZAS ──────────────────────────────────────────────
    Route::prefix('finances')->group(function () {
        Route::get('/',                       [FinanceController::class, 'index']);
        Route::post('/',                      [FinanceController::class, 'store']);
        Route::get('/statistics/summary',     [FinanceController::class, 'statistics']);
        Route::post('/analyze',               [FinanceController::class, 'analyze']);
        Route::get('/{id}',                   [FinanceController::class, 'show']);
        Route::put('/{id}',                   [FinanceController::class, 'update']);
        Route::delete('/{id}',                [FinanceController::class, 'destroy']);
        Route::patch('/{id}/pay-installment', [FinanceController::class, 'payDebtInstallment']);
        Route::post('/ajax',                  [FinanceController::class, 'storeAjax']);
    });

    Route::prefix('client')->group(function () {
        Route::get('/finances', [FinanceController::class, 'index']);
        Route::post('/income', function (Request $request) {
            $request->merge(['type' => 'income']);
            return app(FinanceController::class)->store($request);
        });
        Route::post('/expense', function (Request $request) {
            $request->merge(['type' => 'expense']);
            return app(FinanceController::class)->store($request);
        });
        Route::post('/investment', function (Request $request) {
            $request->merge(['type' => 'investment']);
            return app(FinanceController::class)->store($request);
        });
        Route::post('/debt', function (Request $request) {
            $request->merge(['type' => 'debt']);
            return app(FinanceController::class)->store($request);
        });
        Route::post('/inventory', function (Request $request) {
            $request->merge(['type' => 'inventory']);
            return app(FinanceController::class)->store($request);
        });
        Route::post('/costs', function (Request $request) {
            $request->merge(['type' => 'costs']);
            return app(FinanceController::class)->store($request);
        });
        Route::put('/finances/{id}',    [FinanceController::class, 'update']);
        Route::delete('/finances/{id}', [FinanceController::class, 'destroy']);
        Route::patch('/debt/{id}/pay',  [FinanceController::class, 'payDebtInstallment']);
    });

    Route::post('/client/finances/export-pdf', [FinanceExportController::class, 'exportPDF']);

    // ── GANADO / HATO ─────────────────────────────────────────
    Route::get('hato',             [CattleController::class, 'index']);
    Route::post('hato',            [CattleController::class, 'store']);
    Route::get('hato/mothers',     [CattleController::class, 'mothers']);
    Route::post('hato/birth',      [CattleController::class, 'registerBirth']);
    Route::get('hato/{id}',        [CattleController::class, 'show']);
    Route::post('hato/{id}',       [CattleController::class, 'update']);
    Route::put('hato/{id}',        [CattleController::class, 'update']);
    Route::delete('hato/{id}',     [CattleController::class, 'destroy']);
    Route::get('cattles',          [CattleController::class, 'index']);
    Route::post('cattles',         [CattleController::class, 'store']);
    Route::get('cattles/{id}',     [CattleController::class, 'show']);
    Route::post('cattles/{id}',    [CattleController::class, 'update']);
    Route::delete('cattles/{id}',  [CattleController::class, 'destroy']);
    Route::post('cattles/{id}/birth', [CattleController::class, 'registerBirth']);

    // ── NOTIFICACIONES ────────────────────────────────────────
    Route::get('notificaciones',             [NotificationController::class, 'getMyNotifications']);
    Route::get('notificaciones/no-leidas',   [NotificationController::class, 'getMyUnreadCount']);
    Route::post('notificaciones/leer-todas', [NotificationController::class, 'markAllMyRead']);
    Route::post('notificaciones/{id}/leer',  [NotificationController::class, 'markRead']);
    Route::delete('notificaciones/todas',    [NotificationController::class, 'destroyAll']);
    Route::delete('notificaciones/{id}',     [NotificationController::class, 'destroy']);
    Route::get('/notifications/{userId}',              [NotificationController::class, 'index']);
    Route::get('/notifications/{userId}/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/notifications/{id}/read',           [NotificationController::class, 'markRead']);
    Route::patch('/notifications/{userId}/read-all',   [NotificationController::class, 'markAllRead']);

    // ── COMUNIDAD ─────────────────────────────────────────────
    // ⚠️ Rutas estáticas SIEMPRE antes de las que tienen {id}
    Route::get('comments/liked',          [RecommendationController::class, 'liked']);
    Route::get('recommendations/liked',   [RecommendationController::class, 'liked']);

    Route::get('recommendations',         [RecommendationController::class, 'index']);
    Route::post('recommendations',        [RecommendationController::class, 'store']);
    Route::get('recommendations/{id}',    [RecommendationController::class, 'show']);

    Route::get('comments',                [RecommendationController::class, 'index']);
    Route::post('comments',               [RecommendationController::class, 'store']);
    Route::get('comments/{id}',           [RecommendationController::class, 'show']);
    Route::delete('comments/{id}',        [RecommendationController::class, 'destroy']);
    Route::post('comments/{id}/reply',    [RecommendationController::class, 'reply']);
    Route::post('comments/{id}/like',     [RecommendationController::class, 'toggleLike']);

    // ── PRODUCCIÓN ANIMAL ─────────────────────────────────────
    Route::get('animal_productions',                     [AnimalProductionController::class, 'index']);
    Route::post('animal_productions',                    [AnimalProductionController::class, 'store']);
    Route::get('animal_productions/{animal_production}', [AnimalProductionController::class, 'show']);

    // ── GALLINAS ──────────────────────────────────────────────
    Route::get('hens',       [HenController::class, 'index']);
    Route::post('hens',      [HenController::class, 'store']);
    Route::get('hens/{hen}', [HenController::class, 'show']);

    // ── CULTIVOS ──────────────────────────────────────────────
    Route::get('crops',        [CropController::class, 'index']);
    Route::post('crops',       [CropController::class, 'store']);
    Route::get('crops/{crop}', [CropController::class, 'show']);

    // ── CAFÉ ──────────────────────────────────────────────────
    Route::get('coffe_crops',              [CoffeCropController::class, 'index']);
    Route::post('coffe_crops',             [CoffeCropController::class, 'store']);
    Route::get('coffe_crops/{coffe_crop}', [CoffeCropController::class, 'show']);

    // ── AGUACATE ──────────────────────────────────────────────
    Route::get('avocado_crops',                [AvocadoCropController::class, 'index']);
    Route::post('avocado_crops',               [AvocadoCropController::class, 'store']);
    Route::get('avocado_crops/{avocado_crop}', [AvocadoCropController::class, 'show']);
});

// ── ADMIN ─────────────────────────────────────────────────────
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::middleware(['admin.token'])->prefix('admin')->group(function () {
    Route::post('/logout',   [AdminAuthController::class, 'logout']);
    Route::get('/me',        [AdminAuthController::class, 'me']);
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    Route::get('/users',               [AdminUsersController::class, 'index']);
    Route::get('/users/{id}',          [AdminUsersController::class, 'show']);
    Route::patch('/users/{id}/toggle', [AdminUsersController::class, 'toggle']);
    Route::delete('/users/{id}',       [AdminUsersController::class, 'destroy']);

    Route::get('/finances',               [AdminFinancesController::class, 'index']);
    Route::get('/finances/user/{userId}', [AdminFinancesController::class, 'byUser']);

    Route::get('/comments',               [AdminCommentsController::class, 'index']);
    Route::get('/comments/user/{userId}', [AdminCommentsController::class, 'byUser']);
    Route::delete('/comments/{id}',       [AdminCommentsController::class, 'destroy']);
});