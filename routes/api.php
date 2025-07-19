<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\PostController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware(Authenticate::using('sanctum'));

//posts
Route::apiResource('/posts', PostController::class);

//register
Route::post('/register', [AuthController::class, 'register']);

//login
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    //logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
