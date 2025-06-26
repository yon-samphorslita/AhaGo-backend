<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverProfileController;
use App\Http\Controllers\DriverSectionController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\RestaurantProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\FoodItemReviewController;
use App\Http\Controllers\RestaurantReviewController;
use App\Http\Controllers\ReviewController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::middleware('auth:sanctum')->get('/currentUser', [AuthController::class, 'currentUser']);
});
Route::middleware('auth:sanctum')->post('/photo-upload', [UploadController::class, 'uploadPhoto']);

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/{role}/login', [AuthController::class, 'login']);

// Driver Sections
Route::controller(DriverSectionController::class)->prefix('driver-sections')->group(function(){
    Route::get('/', 'getSections');
    Route::post('/', 'createSection');
});

// Driver Buttons
Route::controller(DriverSectionController::class)->prefix('driver-buttons')->group(function(){
    Route::get('/', 'getButtons');
    Route::post('/', 'createButton');
});

// Customer Profiles - renamed prefix to avoid conflict
Route::controller(CustomerProfileController::class)->prefix('customerProfiles')->group(function() {
    Route::get('/', 'getCustomers');
    Route::get('/count', 'getCustomersCount');
    Route::post('/', 'createCustomer');
});
Route::middleware('auth:sanctum')->put('/customer/profile', [CustomerProfileController::class, 'updateCustomerProfile']);

// Restaurant Profiles
Route::controller(RestaurantProfileController::class)->prefix('restaurants')->group(function() {
    Route::get('/', 'getRestaurants');
    Route::post('/', 'createRestaurant');
});

// Drivers
Route::controller(DriverProfileController::class)->prefix('drivers')->group(function() {
    Route::get('/', 'getDrivers');
    Route::post('/', 'createDriver');
});
Route::middleware('auth:sanctum')->put('/driver/profile', [DriverProfileController::class, 'updateDriverProfile']);

// Orders
Route::controller(OrderController::class)->prefix('orders')->group(function() {
    Route::get('/', 'getOrders');
    Route::get('/rest/{restId}', 'getOrdersByRest');
    Route::get('/recent/{restId}', 'getRecentOrders');    // last 7 days on dashboard
    Route::post('/', 'createOrder');
    Route::get('/{orderId}', 'getOrder');
    Route::patch('/{orderId}', 'updateOrder');
    Route::delete('/{orderId}', 'deleteOrder');
    Route::patch('/{orderId}/status', 'updateOrderStatus');    // Update status only
    Route::post('/assign', 'assignOrderToDriver');             // Assign driver to order
    Route::get('/{orderId}/details', 'showOrderDetails');      // Detailed order info
});
// Route::middleware('auth:sanctum')->post('/orders', [OrderController::class, 'createOrder']);

// User management
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);          // List all users
    Route::get('/{id}', [UserController::class, 'show']);       // Show user by ID
    Route::post('/', [UserController::class, 'store']);         // Create new user
    Route::put('/{id}', [UserController::class, 'update']);     // Update user by ID
    Route::post('/{id}', [UserController::class, 'update']);    // _method override (POST acting as PUT)
    Route::delete('/{id}', [UserController::class, 'destroy']); // Delete user by ID
    Route::post('/{id}/verify', [UserController::class, 'verify']);  // Verify user and send email
});

// Messages
Route::get('/messages', [MessageController::class, 'index']);       // List messages (filter by sender_id & receiver_id)
Route::post('/messages', [MessageController::class, 'store']);      // Create new message
Route::patch('/messages/{id}/read', [MessageController::class, 'markAsRead']); // Mark message as read

// Restaurants (rests)
Route::controller(RestaurantController::class)->prefix('rests')->group(function() {
    Route::get('/','getAllRests');
    Route::post('/','createRest');
    Route::get('/{restId}','getRest');
    Route::patch('/{restId}','updateRest');
    Route::delete('/{restId}','deleteRest');
});
Route::patch('/restaurants/{id}', [RestaurantProfileController::class, 'updateRestaurant']);

// Categories
Route::controller(CategoryController::class)->prefix('categories')->group(function() {
    Route::get('/','getCategories');
    Route::post('/','createCategory');
    Route::get('/{categId}','getCategory');
    Route::patch('/{categId}','updateCategory');
    Route::delete('/{categId}','deleteCategory');
});
Route::get('/restaurants/{restaurantId}/categories', [CategoryController::class, 'getCategoriesByRestaurant']);

// Food Items
Route::controller(FoodItemController::class)->prefix('foodItems')->group(function() {
    Route::get('/','getFoodItems');
    Route::get('/count','getFoodItemsCount');
    Route::post('/','createFoodItem');
    Route::get('/stock','getStockLevel');
    Route::get('/top','getTopSellers'); // get 10 most sold items
    Route::get('/top/{restId}','getTopSellersOfRest'); // get 10 most sold items
    Route::get('/{foodItemId}','getFoodItem');
    Route::get('/rest/{restId}','getFoodItemsByRestId');
    Route::patch('/{foodItemId}','updateFoodItem');
    Route::delete('/{foodItemId}','deleteFoodItem');
});

Route::get('/restaurants/{id}/foodItems', [FoodItemController::class, 'getFoodItemsByRestaurant']);

Route::controller(OrderController::class)->prefix('orders')->group(function() {
    Route::get('/','getOrders');
    Route::get('/count','getOrdersCount');
    Route::get('/orderTypes','getOrdersTypes');
    Route::get('/rest/{restId}','getOrdersByRest');
    Route::get('/recent/{restId}','getRecentOrders');    // last 7 days on dashboard
    Route::post('/','createOrder');
    Route::get('/{orderId}','getOrder');
    Route::patch('/{orderId}','updateOrder');
    Route::delete('/{orderId}','deleteOrder');
});

// Order Items
Route::controller(OrderItemController::class)->prefix('orderItems')->group(function() {
    Route::get('/','getAllOrderItems');
    Route::get('/{restId}','getAllOrderItemsById');
    Route::get('/topCategories','getTopCategories');
    Route::post('/','createOrderItem');
    Route::get('/{orderItemId}', 'getOrderItem');  // singular name
    Route::patch('/{orderItemId}','updateOrderItem');
    Route::delete('/{orderItemId}','deleteOrderItem');
});


// Customers
Route::controller(CustomerController::class)->prefix('customers')->group(function() {
    Route::get('/','getCustomers');
    Route::post('/','createCustomer');
    Route::get('/{customerId}','getCustomer');
    Route::patch('/{customerId}','updateCustomer');
    Route::delete('/{customerId}','deleteCustomer');
});

// Transactions
Route::controller(TransactionController::class)->prefix('transactions')->group(function() {
    Route::get('/','getTransactions');
    Route::get('/revenue','getRevenue');
    Route::get('/recent/{restId}','getRecentTransactions'); 
    Route::post('/','createTransaction');
    Route::get('/{tId}','getTransaction');
    Route::get('/rest/{rId}','getAllByRestId');
    Route::delete('/{tId}','deleteTransaction');
});

// Notifications
Route::controller(NotificationController::class)->prefix('notifications')->group(function() {
    Route::get('/','getNotifications');
    Route::get('/driver/{driverId}','getDriverNotifications');
    Route::get('/rest/{restId}','getOwnerNotifications');
    Route::post('/','createNotification');
});

// Uploads
Route::post('/upload', [UploadController::class, 'upload']);
Route::controller(UploadController::class)->prefix('upload')->group(function() {
    Route::post('/', 'upload');
});

// Banner and Review APIs
Route::apiResource('banners', BannerController::class);
Route::apiResource('reviews', ReviewController::class);

Route::controller(RestaurantReviewController::class)->prefix('restaurant_reviews')->group(function() {
    Route::get('/','getReviews');
    Route::post('/','createReview');
    Route::get('/{restId}','getReviewsByRestaurant');
    Route::patch('/{reviewId}','updateReview');
    Route::delete('/{reviewId}','deleteReview');
});

Route::controller(FoodItemReviewController::class)->prefix('foodItem_reviews')->group(function() {
    Route::get('/','getReviews');
    Route::post('/','createReview');
    Route::get('/{food_item_id}','getReviewsByFoodItem');
    Route::patch('/{reviewId}','updateReview');
    Route::delete('/{reviewId}','deleteReview');
});


