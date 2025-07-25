<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;

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
Route::resource('assets', AssetController::class)->only([
    'store', 'show', 'update', 'destroy'
]);
Route::get('/users/{userID}/assets', [AssetController::class, 'getAssetsByUserID']);


Route::resource('users', UserController::class)->only([
    'index', 'show', 'store', 'update', 'destroy'
]);

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/refresh-token', 'refresh');
});

Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);


Route::middleware(['jwt.auth'])->get('/my-assets', [AssetController::class, 'getMyAssets']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
