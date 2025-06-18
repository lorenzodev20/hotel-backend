<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'login'])
            ->name('login')->withoutMiddleware(['auth:sanctum']);
        Route::post('logout', [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'logout']);
        Route::post('me', [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'me']);
        Route::post('refresh', [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'refresh']);
        Route::post('register', [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'register'])
            ->withoutMiddleware(['auth:sanctum']);
    });

    Route::prefix('catalogs')->group(function () {
        Route::get('accommodations', [\App\Http\Controllers\Api\V1\Catalog\AccommodationTypeController::class, 'index']);
        Route::get('rooms-types', [\App\Http\Controllers\Api\V1\Catalog\RoomTypeController::class, 'index']);
        Route::get('states', [\App\Http\Controllers\Api\V1\Catalog\StateController::class, 'index']);
        Route::get('cities', [\App\Http\Controllers\Api\V1\Catalog\CityController::class, 'index']);
        Route::get('rules', [\App\Http\Controllers\Api\V1\Catalog\RoomRuleController::class, 'index']);
    });

    Route::apiResource('hotel', App\Http\Controllers\Api\V1\Hotel\HotelController::class);
    Route::apiResource('availability', App\Http\Controllers\Api\V1\Hotel\HotelAvailabilityController::class);
});
