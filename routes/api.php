<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/





// auth
Route::prefix('/v1/')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot_password', [AuthController::class, 'forgot_password']);
    Route::post('reset_password',[AuthController::class,"reset_password"])->name("password.reset");
});


// authenticated route
Route::middleware('auth:sanctum')->prefix('/v1')->group(function () {
    Route::post("/blog", [BlogController::class, 'store']);
    Route::patch("/blog/{id}", [BlogController::class, "update"]);

    // category
    Route::post("/category", [CategoryController::class, "store"]);
});

// data view
Route::prefix("/v1")->group(function () {
    Route::get("/blog", [BlogController::class, 'index']);
    Route::get("/blog/{id}", [BlogController::class, "show"]);

    // category
    Route::get('/category', [CategoryController::class, "index"]);
});
