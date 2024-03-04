<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
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

// Route::get('/users',[UserController::class,'index']);
// Route::get('/user/create',[UserController::class,'index']);
// Route::post('/user',[UserController::class,'store']);


Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
Route::middleware('auth:api')->group(function(){
    Route::get('user-info',[AuthController::class,'userInfo']);
    Route::get('users',[AuthController::class,'users']);
    Route::post('user/create',[AuthController::class,'createUser']);
});