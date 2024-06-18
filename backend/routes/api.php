<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandsController;
use App\Http\Controllers\Api\ItemsController;
use App\Http\Controllers\Api\CategoriesContoller;
use App\Http\Controllers\Api\FavoriteItemsController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FinancesController;
use App\Http\Controllers\Api\SpecificationTitleController;


Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    Route::put('profile/edit/{id}', [AuthController::class, 'update']);
    Route::put('profile/change-password/{id}', [AuthController::class, 'changePassword']);

    // Brands
    Route::post('brands', [BrandsController::class, 'store']);
    Route::get('brands/{id}/edit', [BrandsController::class, 'edit']);
    Route::delete('brands/{id}/delete', [BrandsController::class, 'destroy']);

    // Items
    Route::post('items', [ItemsController::class, 'store']);
    Route::get('items/{id}/edit', [ItemsController::class, 'edit']);
    Route::put('items/{id}/edit', [ItemsController::class, 'update']);
    Route::delete('items/{id}/delete', [ItemsController::class, 'destroy']);
    Route::post('/items/purchase', [ItemsController::class, 'purchase']);

    // Categories
    Route::post('categories', [CategoriesContoller::class, 'store']);
    Route::get('categories/{id}/edit', [CategoriesContoller::class, 'edit']);
    Route::put('categories/{id}/edit', [CategoriesContoller::class, 'update']);
    Route::delete('categories/{id}/delete', [CategoriesContoller::class, 'destroy']);

    // Favorite Items
    Route::post('favorites', [FavoriteItemsController::class, 'store']);
    Route::get('favorites', [FavoriteItemsController::class, 'index']);
    Route::get('favorites/{id}', [FavoriteItemsController::class, 'show']);
    Route::get('favorites/user', [FavoriteItemsController::class, 'getUserFavorites']);

    // Cart
    Route::post('cart', [CartController::class, 'store']);
    Route::get('cart', [CartController::class, 'index']);
    Route::get('cart/{id}', [CartController::class, 'show']);
    Route::get('cart/user', [CartController::class, 'getUserCart']);

    // Purchase
    Route::post('purchase', [PurchaseController::class, 'store']);
    Route::get('purchases/user/{userId}', [PurchaseController::class, 'getUserPurchases']);
    Route::patch('purchases/{id}/status', [PurchaseController::class, 'updateStatus']);
    Route::get('total-spending-per-day', [PurchaseController::class, 'getTotalSpendingPerDay']);
});

// Auth API
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Brands API
Route::get('brands/{id}', [BrandsController::class, 'show']);
Route::get('brands', [BrandsController::class, 'index']);
Route::get('brands/{brandId}/products', [ItemsController::class, 'getItemsByBrand']);
Route::put('brands/{id}/edit', [BrandsController::class, 'update']);

// Items API
Route::get('items', [ItemsController::class, 'index']);
Route::get('items/{id}', [ItemsController::class, 'show']);
Route::get('/front-page-items', [ItemsController::class, 'frontPageItems']);
Route::get('/items/search', [ItemsController::class, 'searchItems']);
Route::get('items-category-count', [ItemsController::class, 'getCategoryItemCount']);
Route::get('/items/similar/{id}', [ItemsController::class, 'getSimilarItems']);
Route::put('items/{id}/update-inventory', [ItemsController::class, 'updateInventory']);

// Categories API
Route::get('categories', [CategoriesContoller::class, 'index']);
Route::get('categories/{id}', [CategoriesContoller::class, 'show']);

// Favorite Items API
Route::get('favorites', [FavoriteItemsController::class, 'index']);
Route::get('user/{id}/favorites-count', [FavoriteItemsController::class, 'userFavoritesCount']);
Route::get('user/{userId}/favorite/{itemId}', [FavoriteItemsController::class, 'isFavorite']);
Route::delete('favorites/item/{item_id}-{user_id}', [FavoriteItemsController::class, 'destroyByItemId']);
Route::get('favorites/user/{userId}', [FavoriteItemsController::class, 'getUserFavorites']);
Route::get('favorites/user/{userId}', [FavoriteItemsController::class, 'userFavorites']);
Route::delete('favorites/user/{userId}/clear', [FavoriteItemsController::class, 'clearUserFavorites']);

// Users API
Route::get('user-amount', [AuthController::class, 'userAmount']);
Route::get('user/{userId}/counts', [AuthController::class, 'getUserCounts']);
Route::post('user/favorites-status', [FavoriteItemsController::class, 'getFavoritesStatus']);
Route::post('user/cart-status', [CartController::class, 'getCartStatus']);
Route::get('/user-registrations', [UserController::class, 'getUserRegistrations']);

// Cart API
Route::get('cart/user/{userId}', [CartController::class, 'getUserCart']);
Route::get('cart/user/{userId}/count', [CartController::class, 'getUserCartCount']);
Route::delete('cart/item/{item_id}-{user_id}', [CartController::class, 'destroyByCartId']);
Route::get('cart/user/{userId}/item/{itemId}', [CartController::class, 'isItemInCart']);
Route::delete('cart/clear/{userId}', [CartController::class, 'clearCartByUserId']);

// Admin purchases API
Route::patch('/items/{id}', [ItemsController::class, 'updateItem']);

// Finance API
Route::get('/finances', [FinancesController::class, 'getFinances']);

// Specifications API
Route::get('/specification_titles/{categoryId}', [ItemsController::class, 'getSpecificationTitles']);
Route::get('/items/{itemId}/specifications', [ItemsController::class, 'getSpecifications']);
Route::get('/specification-titles', [SpecificationTitleController::class, 'index']);
Route::get('/specification-titles/{id}', [SpecificationTitleController::class, 'show']);
Route::put('/specification-titles/{id}', [SpecificationTitleController::class, 'update']);
Route::post('/specification-titles', [SpecificationTitleController::class, 'store']);
Route::delete('/specification-titles/{id}/delete', [SpecificationTitleController::class, 'destroy']);
Route::get('/specification-descriptions', [ItemsController::class, 'getSpecificationsDescription']);


Route::middleware('auth:sanctum')->get('purchases', [PurchaseController::class, 'getAllPurchases']);

