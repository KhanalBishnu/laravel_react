<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
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
Route::post('/products',[ProductController::class,'NoAuthProduct']);
Route::get('/product/details/{id}',[ProductController::class,'NoAuthProductDetail']);


Route::middleware('auth:api')->group(function(){
    Route::get('permissions',[AuthController::class,'getPermissions']);
    Route::get('user-info',[AuthController::class,'userInfo']);
    Route::get('users',[AuthController::class,'users']);
    Route::post('user/create',[AuthController::class,'createUser']);
    Route::get('user/delete/{id}',[AuthController::class,'deleteUser']);
    Route::get('user/edit/{id}',[AuthController::class,'editUser']);
    Route::prefix('/dashboard')->name('dashboard.')->group(function(){
        Route::controller(ProductController::class)->prefix('/products')->name('product.')->group(function(){
            Route::post('/getList','index')->name('index')->middleware(['can:View|Product']);
            Route::post('','store')->name('store')->middleware(['can:Create|Product']);
            Route::post('/update','update')->name('update')->middleware(['can:Update|Product']);
            Route::get('/delete/{id}','delete')->name('delete')->middleware(['can:Delete|Product']);
        });
        Route::controller(RoleAndPermissionController::class)->prefix('role-and-permission')->name('role-and-permission.')->group(function(){
            Route::get('','index')->name('index')->middleware(['can:View|Role And Permission']);
            Route::post('/store','store')->name('store')->middleware(['can:Create|Role And Permission']);
            Route::get('/delete/{role}','delete')->name('delete')->middleware(['can:Delete|Role And Permission']);
            Route::get('/allPermissionList','allPermissionList')->name('allPermissionList')->middleware(['can:View|Role And Permission']);
            Route::get('/getPermissionList/{role}','getPermissionList')->name('getPermissionList')->middleware(['can:View|Role And Permission']);
            Route::get('/getRolePermission/{role}','getRolePermissions')->name('getRolePermissions')->middleware(['can:View|Role And Permission']);
            Route::post('/update','update')->name('update')->middleware(['can:Update|Role And Permission']);
        });
        Route::controller(UserController::class)->prefix('user-management')->name('user-management.')->group(function(){
            Route::get('','index')->name('index')->middleware(['can:View|User Management']);
            Route::post('/store','store')->name('store')->middleware(['can:Create|User Management']);
            Route::post('/update','update')->name('update')->middleware(['can:Update|User Management']);
            Route::get('/delete/{role}','delete')->name('delete')->middleware(['can:Delete|User Management']);           
        });
    });
});