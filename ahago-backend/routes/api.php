<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AdminController;
use \App\Http\Controllers\DriverController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AdminController::class)->prefix('admins')->group(function() {
    Route::get('/','getAdmins');
    Route::post('/','createAdmin');
    Route::get('/{adminId}','getAdmin');
    Route::patch('/{adminId}','updateAdmin');
    Route::delete('/{adminId}','deleteAdmin');
});

Route::controller(DriverController::class)->prefix('drivers')->group(function() {
    Route::get('/','getDrivers');
    Route::post('/','createDriver');
    Route::get('/{driverId}','getDriver');
    Route::patch('/{driverId}','updateDriver');
    Route::delete('/{driverId}','deleteDriver');
});