<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\web\Auth\AuthController as WebAuthController;
use App\Http\Controllers\Api\V1\web\NotificationSettingController as WebNotificationSettingController;
use App\Http\Controllers\Api\V1\Web\Super\DistrictAdminController;

Route::prefix('v1/auth')->middleware('guest:sanctum')->group(function(){
    Route::prefix('web')->group(function(){      
        Route::post('login', [WebAuthController::class, 'login']);
        Route::post('forgot-password', [WebAuthController::class, 'forgotPassword']);
        Route::post('reset-password', [WebAuthController::class, 'resetPassword']);
    });


    Route::prefix('mobile')->group(function(){

    });
});

Route::prefix('v1/profile')->middleware('auth:sanctum')->group(function () {

    Route::prefix('web')->group(function () {
        Route::post('logout', [WebAuthController::class, 'logout']);
        Route::get('me', [WebAuthController::class, 'me']);
        Route::post('change-password', [WebAuthController::class, 'changePassword']);
        Route::put('update-profile', [WebAuthController::class, 'updateProfile']);

        Route::get('notification-settings', [WebNotificationSettingController::class, 'show']);
        Route::post('notification-settings', [WebNotificationSettingController::class, 'storeOrUpdate']);
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
      Route::prefix('web')->group(function(){   
     Route::post('register', [WebAuthController::class, 'register']);
      });
});

use App\Http\Controllers\Api\V1\Web\Super\DistrictController;

Route::prefix('v1/super-admin')->middleware(['auth:sanctum'])->group(function () {
      //District Routes
    Route::prefix('districts')->group(function () {
        Route::get('/', [DistrictController::class, 'index']);
        Route::post('/', [DistrictController::class, 'store']);
        Route::put('/{id}', [DistrictController::class, 'update']);
        Route::put('/{id}/toggle', [DistrictController::class, 'activateOrDeactivate']);
        Route::get('/stats', [DistrictController::class, 'statistics']);
    });

    //District Admin Routes
    Route::prefix('district-admins')->group(function () {
        Route::get('/', [DistrictAdminController::class, 'index']);
        Route::post('/', [DistrictAdminController::class, 'store']);
        Route::get('/{id}', [DistrictAdminController::class, 'show']);
        Route::put('/{id}', [DistrictAdminController::class, 'update']);
        Route::put('/{id}/toggle', [DistrictAdminController::class, 'activateOrDeactivate']);
    });
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
