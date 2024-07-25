<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsersController;

use App\Http\Controllers\Api\DevicesController;
use App\Http\Controllers\Api\OpenWeatherConfigController;
use App\Http\Controllers\Api\DevicesDashboardController;
use App\Http\Controllers\Api\DevicesSensorsController;
use App\Http\Controllers\Api\NotificationsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/v1')->group(function () {

    Route::get('/', function () {
        return response()->json([
            'message' => 'Welcome to Moo Nitoring API v1',
            'version' => 1,
        ]);
    })->name('v1.home');

    Route::prefix('users')->controller(UsersController::class)->group(function(){
        Route::post('/register', 'register');
        Route::post('/login', 'login');

        Route::middleware(['auth:sanctum', 'abilities:users'])->group(function () {
            Route::get('/profile', 'profile');
            Route::delete('/logout', 'logout');
        });
    });

    Route::prefix('/devices')->controller(DevicesController::class)->group(function() {
        Route::middleware(['auth:sanctum', 'abilities:users'])->group(function () {
            Route::get('/list', [DevicesController::class, 'list']);
            Route::post('/register', [DevicesController::class, 'register']);
            Route::post('/renew', [DevicesController::class, 'renew']);
            Route::get('/details', [DevicesController::class, 'details']);
        });

        Route::prefix('/controller')->group(function(){  
            Route::get('/current-users', [DevicesController::class, 'current_users'])->middleware(['auth:sanctum', 'abilities:devices']);
            Route::get('/current-devices', [DevicesController::class, 'current_devices'])->middleware(['auth:sanctum', 'abilities:devices']);
            Route::post('/changes', [DevicesController::class, 'changes'])->middleware(['auth:sanctum', 'abilities:users']);
        });

        Route::prefix('/sensor')->controller(DevicesSensorsController::class)->group(function(){
            Route::post('/add', [DevicesSensorsController::class, 'add'])->middleware(['auth:sanctum', 'abilities:devices']);

            Route::middleware(['auth:sanctum', 'abilities:users'])->group(function () {
                Route::get('/data-by-day', [DevicesSensorsController::class, 'byDay']);
                Route::get('/data-by-week', [DevicesSensorsController::class, 'byWeek']);
                Route::get('/data-by-month', [DevicesSensorsController::class, 'byMonth']);
            });
        });
    });

    Route::prefix('/notifications')->controller(NotificationsController::class)->group(function() {
        Route::get('/list', [NotificationsController::class, 'list'])->middleware(['auth:sanctum', 'abilities:users']);
    });


    Route::prefix('/dashboard')->controller(DevicesDashboardController::class)->group(function () {      
        Route::post('/update', [DevicesDashboardController::class, 'update'])->middleware(['auth:sanctum', 'abilities:devices']);
        Route::get('/info', [DevicesDashboardController::class, 'info'])->middleware(['auth:sanctum', 'abilities:users']);
    });

    Route::prefix('/open-weather-token')->controller(OpenWeatherConfigController::class)->middleware(['auth:sanctum', 'abilities:users'])->group(function() {
        Route::post('/add', [OpenWeatherConfigController::class, 'add']);
        Route::get('/info', [OpenWeatherConfigController::class, 'info']);
    });
});
