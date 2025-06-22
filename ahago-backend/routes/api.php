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
use \App\Http\Controllers\NotificationController;
use \App\Http\Controllers\UploadController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ReviewController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {

    Route::middleware('auth:sanctum')->get('/currentUser', [AuthController::class, 'currentUser']);
});
Route::middleware('auth:sanctum')->post('/driver/photo-upload', [UploadController::class, 'uploadDriverPhoto']);

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/{role}/login', [AuthController::class, 'login']);

Route::controller(DriverSectionController::class)->prefix('driver-sections')->group(function(){
    Route::get('/', 'getSections');
    Route::post('/', 'createSection');
});

Route::controller(DriverSectionController::class)->prefix('driver-buttons')->group(function(){
    Route::get('/', 'getButtons');
    Route::post('/', 'createButton');
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
Route::middleware('auth:sanctum')->put('/driver/profile', [DriverProfileController::class, 'updateDriverProfile']);

Route::controller(OrderController::class)->prefix('orders')->group(function() {
    Route::get('/', 'getOrders');
    Route::post('/', 'createOrder');
    Route::patch('/{id}', 'updateOrderStatus');
});

//// test user by sonit
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);          // List all users
    Route::get('/{id}', [UserController::class, 'show']);       // Show user by ID
    Route::post('/', [UserController::class, 'store']);         // Create new user
    Route::put('/{id}', [UserController::class, 'update']);     // Update user by ID
    Route::post('/{id}', [UserController::class, 'update']);    // for _method override (POST acting as PUT)
    Route::delete('/{id}', [UserController::class, 'destroy']); // Delete user by ID
});

//// test message by sonit
Route::get('/messages', [MessageController::class, 'index']);       // List messages (filter by sender_id & receiver_id query params)
Route::post('/messages', [MessageController::class, 'store']);      // Create new message
Route::patch('/messages/{id}/read', [MessageController::class, 'markAsRead']); // Mark message as read

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

Route::get('/restaurants/{restaurantId}/categories', [CategoryController::class, 'getCategoriesByRestaurant']);

Route::controller(FoodItemController::class)->prefix('foodItems')->group(function() {
    Route::get('/','getFoodItems');
    Route::post('/','createFoodItem');
    Route::get('/{foodItemId}','getFoodItem');
    Route::patch('/{foodItemId}','updateFoodItem');
    Route::delete('/{foodItemId}','deleteFoodItem');
});

Route::get('/restaurants/{id}/foodItems', [FoodItemController::class, 'getFoodItemsByRestaurant']);

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

Route::controller(NotificationController::class)->prefix('notifications')->group(function() {
    Route::get('/','getNotifications');
    Route::get('/driver/{driverId}','getDriverNotifications');
});

Route::post('/upload', [UploadController::class, 'upload']);

Route::controller(UploadController::class)->prefix('upload')->group(function() {
    Route::post('/', 'upload');
});
Route::apiResource('banners', BannerController::class);
Route::apiResource('reviews', ReviewController::class);
Route::post('/orders/details', [OrderController::class, 'getOrderDetails']);

