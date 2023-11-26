<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Authcontroller;
use App\Http\Controllers\Api\Testcontroller;
use App\Http\Controllers\Api\Brandscontroller;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('test', [Testcontroller::class, 'index']);
    Route::post('test', [Testcontroller::class, 'store']);
    Route::get('test/{id}', [Testcontroller::class, 'show']);
    Route::get('test/{id}/edit', [Testcontroller::class, 'edit']);
    Route::put('test/{id}/edit', [Testcontroller::class, 'update']);
    Route::delete('test/{id}/delete', [Testcontroller::class, 'destroy']);
    Route::get('logout', [Authcontroller::class, 'logout']);
    Route::get('user', [Authcontroller::class, 'user']);
    Route::get('brands', [Brandscontroller::class, 'index']);
    Route::post('brands', [Brandscontroller::class, 'store']);
    Route::get('brands/{id}', [Brandscontroller::class, 'show']);
    Route::get('brands/{id}/edit', [Brandscontroller::class, 'edit']);
    Route::put('brands/{id}/edit', [Brandscontroller::class, 'update']);
    Route::delete('brands/{id}/delete', [Brandscontroller::class, 'destroy']);
});

//Auth API
Route::post('register', [Authcontroller::class, 'register']);
Route::post('login', [Authcontroller::class, 'login']);

