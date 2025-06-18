<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use \App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverProfileController;
use \App\Http\Controllers\DriverSectionController;
use App\Http\Controllers\FoodItemController;
use \App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;
use \App\Http\Controllers\CustomerProfileController;
use \App\Http\Controllers\RestaurantProfileController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(UserController::class)->prefix('users')->group(function() {
    Route::get('/','getAllUsers');
    Route::post('/','createUser');
    Route::get('/{userId}','getUser');
    Route::patch('/{userId}','updateUser');
    Route::delete('/{userId}','deleteUser');
});

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


Route::controller(RestaurantController::class)->prefix('rests')->group(function() {
    Route::get('/','getAllRests');
    Route::post('/','createRest');
    Route::get('/{restId}','getRest');
    Route::patch('/{restId}','updateRest');
    Route::delete('/{restId}','deleteRest');
});

Route::controller(CategoryController::class)->prefix('categories')->group(function() {
    Route::get('/','getCategories');
    Route::post('/','createCategory');
    Route::get('/{categId}','getCategory');
    Route::patch('/{categId}','updateCategory');
    Route::delete('/{categId}','deleteCategory');
});

Route::controller(FoodItemController::class)->prefix('foodItems')->group(function() {
    Route::get('/','getFoodItems');
    Route::post('/','createFoodItem');
    Route::get('/{foodItemId}','getFoodItem');
    Route::patch('/{foodItemId}','updateFoodItem');
    Route::delete('/{foodItemId}','deleteFoodItem');
});

Route::controller(OrderController::class)->prefix('orders')->group(function() {
    Route::get('/','getOrders');
    Route::post('/','createOrder');
    Route::get('/{orderId}','getOrder');
    Route::patch('/{orderId}','updateOrder');
    Route::delete('/{orderId}','deleteOrder');
});

Route::controller(OrderItemController::class)->prefix('orderItems')->group(function() {
    Route::get('/','getAllOrderItems');
    Route::post('/','createOrderItem');
    Route::get('/{orderId}','getOrderItems');
    Route::patch('/{orderId}','updateOrderItem');
    Route::delete('/{orderId}','deleteOrderItem');
});

Route::controller(CustomerController::class)->prefix('customers')->group(function() {
    Route::get('/','getCustomers');
    Route::post('/','createCustomer');
    Route::get('/{customerId}','getCustomer');
    Route::patch('/{customerId}','updateCustomer');
    Route::delete('/{customerId}','deleteCustomer');
});

