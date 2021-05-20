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
    Route::get('todos/pinned/{date}', [TodoController::class,'pinned']);
    Route::get('todos/pin/{id}', [TodoController::class,'pin_todo']);
    Route::get('todos/unpin/{id}', [TodoController::class,'unpin_todo']);
    Route::post('todos', [TodoController::class,'store']);
    Route::delete('todos/{id}', [TodoController::class,'destroy']);
    Route::post('todos/update', [TodoController::class,'postMemo']);
    Route::get('todos/get_date/{date}', [TodoController::class,'show_date']);
    Route::get('todos/get_week/{date}', [TodoController::class,'weeks_data']);
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
