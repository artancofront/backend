<?php

use Illuminate\Http\Request;
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
//Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getUser']);
Route::post('/register-login-otp', [AuthController::class, 'registerOrLoginWithOTP']);
Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
Route::post('/login-pass', [AuthController::class, 'loginWithPassword']);

Route::middleware('auth:sanctum')->get('/reset-password', [UserController::class, 'resetPasswordWithOTP']);
