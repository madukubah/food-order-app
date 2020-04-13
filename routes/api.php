<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::prefix('v1')->group(function () {
//     Route::get('roles', 'Admin\RoleController@index');
// });
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        // Below mention routes are public, user can access those without any restriction.
        // Create New User
        Route::post('register', 'AuthController@register');
        // Login User
        Route::post('login', 'AuthController@login');
        
        // Refresh the JWT Token
        Route::get('refresh', 'AuthController@refresh');
        
        // Below mention routes are available only for the authenticated users.
        Route::middleware('auth:api')->group(function () {
            // update
            Route::post('update', 'AuthController@update');
            // uploadProfilPict
            Route::post('upload-photo', 'AuthController@uploadProfilPict');
            // Get user info
            Route::get('user', 'AuthController@user');
            // Logout user from application
            Route::post('logout', 'AuthController@logout');
        });
    });

    Route::middleware(['auth:api', 'role:1'])->group(function () {
        Route::resource('/menus', 'Admin\MenuController')->only('store', 'update', 'destroy');
        Route::resource('/customers', 'Admin\CustomerController')->only('index', 'show', 'update', 'destroy' );

        Route::resource('/products', 'Admin\ProductController')->only('store', 'update', 'destroy');
        Route::post('/add-image/{product_id}', 'Admin\ProductController@addImage');
        Route::delete('/delete-image/{image_id}', 'Admin\ProductController@destroyImage');

        Route::get('/get-orders/{status}', 'User\OrderController@getOrders');

    });
    
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/menus', 'Admin\MenuController@index');
        Route::get('/products', 'Admin\ProductController@index');
    });

    Route::middleware(['auth:api',  'role:0'])->group(function () {
        Route::resource('/orders', 'User\OrderController')->only('index', 'store', 'update', 'destroy');
        Route::put('/cancel-orders/{payment_id}', 'User\OrderController@cancelOrder');
        Route::post('/trf-img/{payment_id}', 'User\OrderController@addTrfImage');
    });

});