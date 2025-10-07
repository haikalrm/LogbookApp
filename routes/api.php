<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LogbookController;
use App\Http\Controllers\Api\LogbookItemController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test routes (no authentication required)
Route::get('/ping', [TestController::class, 'ping']);
Route::get('/system-info', [TestController::class, 'systemInfo']);

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Test authenticated route
        Route::get('/ping-auth', [TestController::class, 'authenticatedPing']);
        
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        
        // Logbook routes
        Route::apiResource('logbooks', LogbookController::class);
        Route::post('/logbooks/{id}/approve', [LogbookController::class, 'approve']);
        Route::post('/logbooks/{id}/sign', [LogbookController::class, 'sign']);
        Route::get('/logbooks-statistics', [LogbookController::class, 'statistics']);
        
        // Logbook Items routes
        Route::prefix('logbooks/{logbookId}')->group(function () {
            Route::get('/items', [LogbookItemController::class, 'index']);
        });
        Route::apiResource('logbook-items', LogbookItemController::class);
        Route::get('/logbook-items/by-teknisi', [LogbookItemController::class, 'getByTeknisi']);
        Route::get('/teknisi-summary', [LogbookItemController::class, 'teknisiSummary']);
        
        // Units routes
        Route::apiResource('units', UnitController::class);
        
        // Users routes
        Route::apiResource('users', UserController::class);
        Route::get('/technicians', [UserController::class, 'technicians']);
        Route::get('/positions', [UserController::class, 'positions']);
        
        // Notifications routes
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::post('/', [NotificationController::class, 'store']);
            Route::patch('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
            Route::patch('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [NotificationController::class, 'destroy']);
        });
    });
});

// Legacy route for compatibility
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
