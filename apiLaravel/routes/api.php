<?php

use App\Events\NotificationTest;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\EsewaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleAndPermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Exception\RequestException;


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

Route::get('/send-notification', function (Request $request) {

    
    try {
        return  true;
        $client = new \GuzzleHttp\Client();
    $response = $client->request('GET', 'http://localhost:8000/api/endpoint');
    dd('true');
    // Process the response here
} catch (RequestException $e) {
    // Log the error message
    echo 'Request failed: ' . $e->getMessage();
}
    $message = 'i am ready';
    event(new NotificationTest($message));
    return response()->json(['status' => 'Notification Sent!']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->post('/broadcasting/auth', function () {
    return response()->json(['message' => 'Authenticated']);
});

Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login'])->name('login');

Route::post('/products',[ProductController::class,'NoAuthProduct']);
Route::get('/product/details/{id}',[ProductController::class,'NoAuthProductDetail']);


Route::post('/esewa/initiate', [EsewaController::class, 'initiatePayment']);
Route::post('/esewa/verify', [EsewaController::class, 'verifyPayment']);
Route::get('/esewa/success', [EsewaController::class, 'success'])->name('esewa.success');
Route::get('/esewa/failure', [EsewaController::class, 'failure'])->name('esewa.failure');



Route::middleware('auth:api')->group(function(){
    Route::get('permissions',[AuthController::class,'getPermissions']);
    Route::get('user-info',[AuthController::class,'userInfo']);
    Route::get('users',[AuthController::class,'users']);
    Route::post('user/create',[AuthController::class,'createUser']);
    Route::get('user/delete/{id}',[AuthController::class,'deleteUser']);
    Route::get('user/edit/{id}',[AuthController::class,'editUser']);
    Route::prefix('/dashboard')->name('dashboard.')->group(function(){
        Route::controller(CategoryProductController::class)->prefix('/category-product')->name('category_product.')->group(function(){
            Route::post('/getList','index')->name('index')->middleware(['can:View|Category Product']);
            Route::post('','store')->name('store')->middleware(['can:Create|Category Product']);
            Route::post('/update','update')->name('update')->middleware(['can:Update|Category Product']);
            Route::get('/delete/{id}','delete')->name('delete')->middleware(['can:Delete|Category Product']);
        });
        Route::controller(ProductController::class)->prefix('/products')->name('product.')->group(function(){
            Route::post('/getList','index')->name('index')->middleware(['can:View|Product']);
            Route::post('','store')->name('store')->middleware(['can:Create|Product']);
            Route::post('/update','update')->name('update')->middleware(['can:Update|Product']);
            Route::get('/delete/{id}','delete')->name('delete')->middleware(['can:Delete|Product']);
        });
        Route::controller(RoleAndPermissionController::class)->prefix('role-and-permission')->name('role-and-permission.')->group(function(){
            Route::post('','index')->name('index')->middleware(['can:View|Role And Permission']);
            Route::post('/store','store')->name('store')->middleware(['can:Create|Role And Permission']);
            Route::get('/delete/{role}','delete')->name('delete')->middleware(['can:Delete|Role And Permission']);
            Route::get('/allPermissionList','allPermissionList')->name('allPermissionList')->middleware(['can:View|Role And Permission']);
            Route::get('/getPermissionList/{role}','getPermissionList')->name('getPermissionList')->middleware(['can:View|Role And Permission']);
            Route::get('/getRolePermission/{role}','getRolePermissions')->name('getRolePermissions')->middleware(['can:View|Role And Permission']);
            Route::post('/update','update')->name('update')->middleware(['can:Update|Role And Permission']);
        });
        Route::controller(UserController::class)->prefix('user-management')->name('user-management.')->group(function(){
            Route::post('','index')->name('index')->middleware(['can:View|User Management']);
            Route::post('/store','store')->name('store')->middleware(['can:Create|User Management']);
            Route::post('/update','update')->name('update')->middleware(['can:Update|User Management']);
            Route::get('/delete/{user}','delete')->name('delete')->middleware(['can:Delete|User Management']);           
        });
    });
});