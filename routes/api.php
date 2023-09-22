<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
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

Route::group([
    'prefix' => 'auth',
    'middleware' => 'api'
], function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('me', [AuthController::class, 'me']);
});


Route::group([
    'middleware'    =>  'api',
    'prefix'        =>  'user'
], function ($router) {
    Route::get('/',  [AuthController::class, "index"]);
    Route::get('/{id}',  [AuthController::class, "show"]);
});

// Route::apiResource('user', )

// Route::group([
//     'middleware'    =>  'api',
//     'prefix'        =>  'blog'
// ], function ($router) {
//     Route::get('/',  [AuthController::class, "index"]);
//     Route::get('/{id}',  [AuthController::class, "show"]);
// });
