<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Testcontroller;
use App\Http\Controllers\Api\Authcontroller;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Test API
Route::get('test', [Testcontroller::class, 'index']);
Route::post('test', [Testcontroller::class, 'store']);
Route::get('test/{id}', [Testcontroller::class, 'show']);
Route::get('test/{id}/edit', [Testcontroller::class, 'edit']);
Route::put('test/{id}/edit', [Testcontroller::class, 'update']);
Route::delete('test/{id}/delete', [Testcontroller::class, 'destroy']);

//Auth API
Route::post('register', [Authcontroller::class, 'register']);
Route::post('login', [Authcontroller::class, 'login']);