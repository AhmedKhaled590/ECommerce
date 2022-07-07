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

    Route::resource('/products', ProductsController::class);
    Route::resource('/categories', CategoriesController::class);

    Route::get('/products/{id}/category', [ProductsController::class, 'getCategory']);
    Route::post('/products/addmultiple', [ProductsController::class, 'addMultipleProducts']);

    Route::get('/categories/{id}/products', [ProductsController::class, 'getProducts']);
    Route::post('/categories/addmultiple', [CategoriesController::class, 'addMultipleCategories']);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::post('/cart/decreaseQuantity', [CartController::class, 'decreaseQuantity']);
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
