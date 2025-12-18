<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\web\Auth\AuthController as WebAuthController;
use App\Http\Controllers\Api\V1\web\NotificationSettingController as WebNotificationSettingController;




Route::prefix('v1/auth')->middleware('guest:sanctum')->group(function(){
    Route::prefix('web')->group(function(){      
        Route::post('login', [WebAuthController::class, 'login']);
        Route::post('forgot-password', [WebAuthController::class, 'forgotPassword']);
        Route::post('reset-password', [WebAuthController::class, 'resetPassword']);
    });


    Route::prefix('mobile')->group(function(){

    });
});

Route::prefix('v1/auth')->middleware('auth:sanctum')->group(function () {

    Route::prefix('web')->group(function () {
        Route::post('logout', [WebAuthController::class, 'logout']);
        Route::get('me', [WebAuthController::class, 'me']);
        Route::post('change-password', [WebAuthController::class, 'changePassword']);
        Route::put('update-profile', [WebAuthController::class, 'updateProfile']);

        Route::get('notification-settings', [WebNotificationSettingController::class, 'show']);
        Route::put('notification-settings', [WebNotificationSettingController::class, 'storeOrUpdate']);
    });

    Route::prefix('mobile')->group(function () {
        
    });
});

Route::prefix('v1')->middleware('guest:sanctum')->group(function(){

});


Route::prefix('v1/collectors')->middleware('auth:sanctum')->group(function(){

});

Route::prefix('v1/finance')->middleware('auth:sanctum')->group(function(){

});

Route::prefix('v1/admin')->middleware('auth:sanctum')->group(function(){
     Route::post('register', [WebAuthController::class, 'register']);
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
