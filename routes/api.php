<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleOrderController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

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

Route::group(['middleware' => 'guest:sanctum'], function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/sanctum/token', [LoginController::class, 'authenticate']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    // Users
    Route::get('user', [UsersController::class, 'showByToken'])->name('users.show.token');
    Route::get('users', [UsersController::class, 'index'])->name('users.index');
    Route::post('users', [UsersController::class, 'store'])->name('users.store');
    Route::put('users/{id}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [UsersController::class, 'delete'])->name('users.delete');
    // ----

    // items
    Route::post('items', [ItemController::class, 'store'])->name('item.store');
    Route::get('items', [ItemController::class, 'index'])->name('item.index');

    // Orders
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('order-reports', [OrderController::class, 'reports'])->name('orders.show.reports');
    Route::put('orders/{id}', [OrderController::class, 'update'])->name('orders.update');
    
    // Vehicles
    Route::post('vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('vehicles/{id}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('vehicle-reports', [VehicleController::class, 'reports'])->name('vehicles.show.reports');
    Route::put('vehicles/{id}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('vehicles/{id}', [VehicleController::class, 'delete'])->name('vehicles.delete');

    Route::post('assign-vehicle', [VehicleOrderController::class, 'store'])->name('vehicles.store');
});