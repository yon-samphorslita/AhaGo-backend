<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AdminController;
use \App\Http\Controllers\DriverController;
use \App\Http\Controllers\DriverSectionController;
use \App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;

use \App\Http\Controllers\UserController;
use \App\Http\Controllers\CustomerProfileController;
use \App\Http\Controllers\RestaurantProfileController;
use \App\Http\Controllers\DriverProfileController;
use \App\Http\Controllers\OrderController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::controller(AdminController::class)->prefix('admins')->group(function() {
//     Route::get('/','getAdmins');
//     Route::post('/','createAdmin');
//     Route::get('/{adminId}','getAdmin');
//     Route::patch('/{adminId}','updateAdmin');
//     Route::delete('/{adminId}','deleteAdmin');
// });

// Route::controller(DriverController::class)->prefix('drivers')->group(function() {
//     Route::get('/','getDrivers');
//     Route::post('/','createDriver');
//     Route::get('/{driverId}','getDriver');
//     Route::patch('/{driverId}','updateDriver');
//     Route::delete('/{driverId}','deleteDriver');
// });

Route::controller(DriverSectionController::class)->prefix('driver-sections')->group(function(){
    Route::get('/', 'getSections');
    Route::post('/', 'createSection');
});

Route::controller(DriverSectionController::class)->prefix('driver-buttons')->group(function(){
    Route::get('/', 'getButtons');
    Route::post('/', 'createButton');
});
Route::controller(UserController::class)->prefix('users')->group(function() {
    Route::get('/', 'getUsers');
    Route::post('/', 'createUser');
});

Route::controller(CustomerProfileController::class)->prefix('customers')->group(function() {
    Route::get('/', 'getCustomers');
    Route::post('/', 'createCustomer');
});

Route::controller(RestaurantProfileController::class)->prefix('restaurants')->group(function() {
    Route::get('/', 'getRestaurants');
    Route::post('/', 'createRestaurant');
});

Route::controller(DriverProfileController::class)->prefix('drivers')->group(function() {
    Route::get('/', 'getDrivers');
    Route::post('/', 'createDriver');
});

Route::controller(OrderController::class)->prefix('orders')->group(function() {
    Route::get('/', 'getOrders');
    Route::post('/', 'createOrder');
});

//// test user by sonit
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);          // List all users
    Route::get('/{id}', [UserController::class, 'show']);       // Show user by ID
    Route::post('/', [UserController::class, 'store']);         // Create new user
    Route::put('/{id}', [UserController::class, 'update']);     // Update user by ID
    Route::delete('/{id}', [UserController::class, 'destroy']); // Delete user by ID
});
//// test message by sonit
Route::get('/messages', [MessageController::class, 'index']);       // List messages (filter by sender_id & receiver_id query params)
Route::post('/messages', [MessageController::class, 'store']);      // Create new message
Route::patch('/messages/{id}/read', [MessageController::class, 'markAsRead']); // Mark message as read

/// test login with users table and frontend 
Route::post('/{role}/login', [AuthController::class, 'login']);
/// test signup with user
Route::post('/signup', [AuthController::class, 'signup']);
