<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\CompanyProfileController as AdminCompanyProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SPA Shell
|--------------------------------------------------------------------------
| All GET routes that the Vue Router handles are served with the SPA view.
| POST/PUT/PATCH/DELETE routes remain with their original controllers so
| that existing tests continue to pass.
|
*/

$spa = fn () => view('spa');

// ── Public SPA routes ────────────────────────────────────────────────

Route::get('/', $spa)->name('home');

// ── Public data routes (GET returns Inertia for tests, but also 200) ─

Route::resource('products', ProductController::class)->only(['index', 'show']);
Route::resource('articles', ArticleController::class)->only(['index', 'show']);

// ── Auth SPA pages ───────────────────────────────────────────────────
// These override the GET routes from auth.php, serving the SPA shell.
// POST routes from auth.php still work because they are different methods.

Route::middleware('guest')->group(function () use ($spa) {
    Route::get('register', $spa)->name('register');
    Route::get('login', $spa)->name('login');
    Route::get('forgot-password', $spa)->name('password.request');
    Route::get('reset-password/{token}', $spa)->name('password.reset');
});

// ── Authenticated User Routes ────────────────────────────────────────

Route::middleware('auth')->group(function () use ($spa) {
    // SPA profile page (GET)
    Route::get('/profile', $spa)->name('profile.edit');

    // Profile data operations (POST/PATCH/DELETE) — used by tests
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Comments (any authenticated user can post)
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');

    // Auth pages that require login
    Route::get('verify-email', $spa)->name('verification.notice');
    Route::get('confirm-password', $spa)->name('password.confirm');
});

// Breeze dashboard — serve SPA
Route::get('/dashboard', $spa)->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Dashboard Routes (unchanged — tests depend on these)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::resource('articles', AdminArticleController::class);
    Route::resource('transactions', AdminTransactionController::class);
    Route::get('/company-profile', [AdminCompanyProfileController::class, 'edit'])->name('company-profile.edit');
    Route::put('/company-profile', [AdminCompanyProfileController::class, 'update'])->name('company-profile.update');
});

/*
|--------------------------------------------------------------------------
| Auth POST routes (login, register, logout, password reset, etc.)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| SPA Catch-All — handles all other Vue Router paths
|--------------------------------------------------------------------------
*/

Route::get('/{any}', fn () => view('spa'))
    ->where('any', '^(?!api|sanctum|admin|up).*$');
