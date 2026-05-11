<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CompanyProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public data
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/berita', [ArticleController::class, 'index']);
Route::get('/berita/{id}', [ArticleController::class, 'show']);
Route::get('/rating', [RatingController::class, 'index']);
Route::get('/company-profile', [CompanyProfileController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Authenticated API Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Ratings (any authenticated user)
    Route::post('/rating', [RatingController::class, 'store']);
    Route::put('/rating/{comment}', [RatingController::class, 'update']);
    Route::delete('/rating/{comment}', [RatingController::class, 'destroy']);

    /*
    |----------------------------------------------------------------------
    | Admin-only API Routes
    |----------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('/dashboard/order-stats', [DashboardController::class, 'orderStats']);
        Route::get('/dashboard/top-products', [DashboardController::class, 'topProducts']);

        // Products CRUD
        Route::post('/products', [ProductController::class, 'store']);
        Route::match(['post', 'put'], '/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // Articles (Berita) CRUD
        Route::post('/berita', [ArticleController::class, 'store']);
        Route::match(['post', 'put'], '/berita/{id}', [ArticleController::class, 'update']);
        Route::delete('/berita/{id}', [ArticleController::class, 'destroy']);

        // Transactions (Keuangan) CRUD
        Route::get('/keuangan', [TransactionController::class, 'index']);
        Route::post('/keuangan', [TransactionController::class, 'store']);
        Route::put('/keuangan/{transaction}', [TransactionController::class, 'update']);
        Route::delete('/keuangan/{transaction}', [TransactionController::class, 'destroy']);
    });
});
