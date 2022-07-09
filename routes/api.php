<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StripeController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/login', function () {
    return 'please login';
})->name('login');

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/dashboard', function () {
        return 'dashboard';
    });

    Route::get('/products', [ProductsController::class, 'index']);
    Route::get('/products/{id}', [ProductsController::class, 'show']);
    Route::post('/products', [ProductsController::class, 'store'])->middleware('is_admin');
    Route::put('/products/{id}', [ProductsController::class, 'update'])->middleware('is_admin');
    Route::delete('/products/{id}', [ProductsController::class, 'destroy'])->middleware('is_admin');
    Route::get('/products/{id}/category', [ProductsController::class, 'getCategory']);
    Route::post('/products/addmultiple', [ProductsController::class, 'addMultipleProducts'])->middleware('is_admin');

    Route::get('/categories', [CategoriesController::class, 'index']);
    Route::get('/categories/{id}', [CategoriesController::class, 'show']);
    Route::post('/categories', [CategoriesController::class, 'store'])->middleware('is_admin');
    Route::put('/categories/{id}', [CategoriesController::class, 'update'])->middleware('is_admin');
    Route::delete('/categories/{id}', [CategoriesController::class, 'destroy'])->middleware('is_admin');
    Route::get('/categories/{id}/products', [ProductsController::class, 'getProducts']);
    Route::post('/categories/addmultiple', [CategoriesController::class, 'addMultipleCategories'])->middleware('is_admin');

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store'])->middleware('is_admin');
    Route::post('/cart/decreaseQuantity', [CartController::class, 'decreaseQuantity'])->middleware("is_admin");

    Route::get('stripe', [StripeController::class, 'stripe']);
    Route::post('stripe', [StripeController::class, 'stripePost'])->name('stripe.post');

});
Route::get('/email/verify', function () {
    return 'Check Your Inbox for Email Verification Link';
})->middleware('auth')->name('verification.notice'); //important step to name the route

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth:sanctum'])->name('verification.verify');
