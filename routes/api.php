<?php

use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Register-Login*
Route::post('/ask-otp', [AuthController::class, 'askOTP']);
Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
Route::post('/login-password', [AuthController::class, 'loginWithPassword']);

//Sanctum Authentication
Route::middleware('auth:user')->group(function () {
    Route::post('/reset-password', [AuthController::class, 'resetPasswordWithOTP']);
    Route::get('/show-profile', [UserController::class, 'showProfile']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
});

//Users CRUD
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserController::class, 'index'])->middleware('permission:users,read');
    Route::get('{id}', [UserController::class, 'show'])->middleware('permission:users,read');
    Route::put('{id}', [UserController::class, 'update'])->middleware('permission:users,update');
    Route::delete('{id}', [UserController::class, 'destroy'])->middleware('permission:users,delete');
});

