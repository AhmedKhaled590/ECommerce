<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RegisterController;
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
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/dashboard', function () {
    return 'dashboard';
})->middleware('auth:sanctum');

Route::resource('/products', ProductsController::class)->middleware('auth:sanctum');
Route::resource('/categories', CategoriesController::class);

Route::get('/products/{id}/category', [ProductsController::class, 'getCategory']);
Route::post('/products/addmultiple', [ProductsController::class, 'addMultipleProducts']);

Route::get('/categories/{id}/products', [ProductsController::class, 'getProducts']);
Route::post('/categories/addmultiple', [CategoriesController::class, 'addMultipleCategories']);

Route::resource('/cart', CartController::class)->middleware('auth:sanctum');
