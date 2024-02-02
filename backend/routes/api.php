<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Authcontroller;
use App\Http\Controllers\Api\Testcontroller;
use App\Http\Controllers\Api\Brandscontroller;
use App\Http\Controllers\Api\Itemscontroller;
use App\Http\Controllers\Api\Categoriescontoller;

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
    // Test
    Route::get('test', [Testcontroller::class, 'index']);
    Route::post('test', [Testcontroller::class, 'store']);
    Route::get('test/{id}', [Testcontroller::class, 'show']);
    Route::get('test/{id}/edit', [Testcontroller::class, 'edit']);
    Route::put('test/{id}/edit', [Testcontroller::class, 'update']);
    Route::delete('test/{id}/delete', [Testcontroller::class, 'destroy']);

    // Auth
    Route::get('logout', [Authcontroller::class, 'logout']);
    Route::get('user', [Authcontroller::class, 'user']);
    Route::put('profile/edit/{id}', [Authcontroller::class, 'update']);
    Route::put('profile/change-password/{id}', [Authcontroller::class, 'changePassword']);

    // Brands
    Route::post('brands', [Brandscontroller::class, 'store']);
    Route::get('brands/{id}/edit', [Brandscontroller::class, 'edit']);
    Route::delete('brands/{id}/delete', [Brandscontroller::class, 'destroy']);

    // Items
    Route::post('items', [Itemscontroller::class, 'store']);
    Route::get('items/{id}/edit', [Itemscontroller::class, 'edit']);
    Route::put('items/{id}/edit', [Itemscontroller::class, 'update']);
    Route::delete('items/{id}/delete', [Itemscontroller::class, 'destroy']);

    // Categories
    Route::get('categories', [Categoriescontoller::class, 'index']);
    Route::post('categories', [Categoriescontoller::class, 'store']);
    Route::get('categories/{id}', [Categoriescontoller::class, 'show']);
    Route::get('categories/{id}/edit', [Categoriescontoller::class, 'edit']);
    Route::put('categories/{id}/edit', [Categoriescontoller::class, 'update']);
    Route::delete('categories/{id}/delete', [Categoriescontoller::class, 'destroy']);
});

// Auth API
Route::post('register', [Authcontroller::class, 'register']);
Route::post('login', [Authcontroller::class, 'login']);

// Brands API
Route::get('brands/{id}', [Brandscontroller::class, 'show']);
Route::get('brands', [Brandscontroller::class, 'index']);
Route::get('brands/{id}/products', [Brandscontroller::class, 'index']);
Route::put('brands/{id}/edit', [Brandscontroller::class, 'update']);
// Items API

Route::get('items', [Itemscontroller::class, 'index']);
Route::get('items/{id}', [Itemscontroller::class, 'show']);
Route::get('/front-page-items', [ItemsController::class, 'frontPageItems']);

