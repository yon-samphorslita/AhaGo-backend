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
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;

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

Route::controller(AdminController::class)->prefix('admins')->group(function() {
    Route::get('/','getAdmins');
    Route::post('/','createAdmin');
    Route::get('/{adminId}','getAdmin');
    Route::patch('/{adminId}','updateAdmin');
    Route::delete('/{adminId}','deleteAdmin');
});

Route::controller(DriverProfileController::class)->prefix('drivers')->group(function() {
    Route::get('/','getDrivers');
    Route::post('/','createDriver');
    Route::get('/{driverId}','getDriver');
    Route::patch('/{driverId}','updateDriver');
    Route::delete('/{driverId}','deleteDriver');
});

Route::controller(DriverSectionController::class)->prefix('driver-sections')->group(function(){
    Route::get('/', 'getSections');
    Route::post('/', 'createSection');
});

Route::controller(DriverSectionController::class)->prefix('driver-buttons')->group(function(){
    Route::get('/', 'getButtons');
    Route::post('/', 'createButton');
});

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
    Route::get('/top','getTopSellers'); // get 3 most sold items
    Route::get('/{foodItemId}','getFoodItem');
    Route::get('/rest/{restId}','getFoodItemsByRestId');
    Route::patch('/{foodItemId}','updateFoodItem');
    Route::delete('/{foodItemId}','deleteFoodItem');
});

Route::controller(OrderController::class)->prefix('orders')->group(function() {
    Route::get('/','getOrders');
    Route::get('/rest/{restId}','getOrdersByRest');
    Route::get('/recent/{restId}','getRecentOrders');    // last 7 days on dashboard
    Route::post('/','createOrder');
    Route::get('/{orderId}','getOrder');
    Route::patch('/{orderId}','updateOrder');
    Route::delete('/{orderId}','deleteOrder');
});

Route::controller(OrderItemController::class)->prefix('orderItems')->group(function() {
    Route::get('/','getAllOrderItems');
    Route::post('/','createOrderItem');
    Route::get('/{orderItemId}','getOrderItems');
    Route::patch('/{orderItemId}','updateOrderItem');
    Route::delete('/{orderItemId}','deleteOrderItem');
});

Route::controller(CustomerController::class)->prefix('customers')->group(function() {
    Route::get('/','getCustomers');
    Route::post('/','createCustomer');
    Route::get('/{customerId}','getCustomer');
    Route::patch('/{customerId}','updateCustomer');
    Route::delete('/{customerId}','deleteCustomer');
});

Route::controller(TransactionController::class)->prefix('transactions')->group(function() {
    Route::get('/','getTransactions');
    Route::get('/recent/{restId}','getRecentTransactions'); 
    Route::post('/','createTransaction');
    Route::get('/{tId}','getTransaction');
    Route::get('/rest/{rId}','getAllByRestId');
    // Route::patch('/{customerId}','updateCustomer');
    Route::delete('/{tId}','deleteTransaction');
});

