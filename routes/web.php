<?php

use App\Http\Controllers\User\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/api/documentation', function () {
    return view('swagger.index', [
        'swaggerJsonUrl' => url('/swagger.json'),
        'baseUrl' => url('/')
    ]);
});

Route::get('/test-session', function () {
    if (!session()->has('test_key')) {
        session(['test_key' => now()->toDateTimeString()]);
        return 'Set session!';
    }

    return 'Session found: ' . session('test_key');
});


