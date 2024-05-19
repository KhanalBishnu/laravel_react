<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleAndPermissionController;
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
    Route::get('user/delete/{id}',[AuthController::class,'deleteUser']);
    Route::get('user/edit/{id}',[AuthController::class,'editUser']);
    Route::prefix('/dashboard')->name('dashboard.')->group(function(){
        Route::controller(ProductController::class)->prefix('/products')->name('product.')->group(function(){
            Route::post('/getList','index')->name('index');
            Route::post('','store')->name('store');
            Route::post('/update','update')->name('update');
            Route::get('/delete/{id}','delete')->name('delete');
        });
        Route::controller(RoleAndPermissionController::class)->prefix('role-and-permission')->name('role-and-permission.')->group(function(){
            Route::get('','index')->name('index');
            Route::post('/store','store')->name('store');
            Route::get('/delete/{role}','delete')->name('delete');
            Route::get('/getPermissionList/{role}','getPermissionList')->name('getPermissionList');
            Route::get('/getRolePermission/{role}','getRolePermissions')->name('getRolePermissions');
            Route::post('/update','update')->name('update');


        });
    });
});