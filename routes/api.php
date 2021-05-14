<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\TodoController;
use App\Models\User;
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

Route::post('/auth/register', [AuthController::class, 'register']);

Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/auth/verify/{user}', [AuthController::class, 'verify']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/me', function() {
        return auth()->user();
    });

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('todos', [TodoController::class,'index']);
    Route::post('todos', [TodoController::class,'store']);
    Route::get('todos/get_date/{date}', [TodoController::class,'show_date']);
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
