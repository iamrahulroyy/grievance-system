<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes — no authentication required
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login'])
        ->middleware('throttle:login');
});

/*
|--------------------------------------------------------------------------
| Protected routes — require a valid Sanctum token
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });

    Route::apiResource('complaints', ComplaintController::class);
    Route::post('complaints/{complaint}/assign', [ComplaintController::class, 'assign']);
    Route::get('complaints/{complaint}/activity', [ComplaintController::class, 'activity']);

    Route::apiResource('complaints.comments', CommentController::class)
        ->only(['index', 'store'])
        ->shallow();

    Route::post('complaints/{complaint}/attachments', [AttachmentController::class, 'store']);
    Route::get('attachments/{attachment}', [AttachmentController::class, 'show']);
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy']);
});
