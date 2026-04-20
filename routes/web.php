<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\ComplaintWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthWebController::class, 'login']);
    Route::get('/register', [AuthWebController::class, 'showRegister']);
    Route::post('/register', [AuthWebController::class, 'register']);
});

Route::get('/', fn () => redirect('/dashboard'));

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout']);

    Route::get('/dashboard', [ComplaintWebController::class, 'dashboard']);

    Route::get('/complaints/create', [ComplaintWebController::class, 'create']);
    Route::post('/complaints', [ComplaintWebController::class, 'store']);
    Route::get('/complaints/{complaint}', [ComplaintWebController::class, 'show']);

    Route::post('/complaints/{complaint}/comment', [ComplaintWebController::class, 'comment']);
    Route::patch('/complaints/{complaint}/status', [ComplaintWebController::class, 'updateStatus']);
    Route::post('/complaints/{complaint}/assign', [ComplaintWebController::class, 'assign']);
    Route::get('/attachments/{attachment}', [ComplaintWebController::class, 'downloadAttachment']);
    Route::post('/complaints/{complaint}/upload', [ComplaintWebController::class, 'uploadAttachment']);
});
