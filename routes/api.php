<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\DevicesController;
use App\Http\Controllers\Api\DevicesSensorsController;

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

    Route::get('/fcm', [UsersController::class, 'toFcm']);

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
            Route::get('/', [DevicesController::class, 'index']);
            Route::post('/register', [DevicesController::class, 'register']);
            Route::post('/renew', [DevicesController::class, 'renew']);
            Route::get('/details', [DevicesController::class, 'details']);
        });

        Route::prefix('/controller')->group(function(){
            Route::get('/current-devices', [DevicesController::class, 'current_devices'])->middleware(['auth:sanctum', 'abilities:devices']);
            Route::get('/current-users', [DevicesController::class, 'current_users'])->middleware(['auth:sanctum', 'abilities:users']);
            Route::post('/changes', [DevicesController::class, 'changes'])->middleware(['auth:sanctum', 'abilities:users']);
        });

        Route::prefix('/sensor')->controller(DevicesSensorsController::class)->group(function(){
            Route::post('/add', 'add')->middleware(['auth:sanctum', 'abilities:devices']);
            Route::get('/data-by-id', 'data_by_id')->middleware(['auth:sanctum', 'abilities:users']);
            Route::get('/data-by-day', 'byDay')->middleware(['auth:sanctum', 'abilities:users']);
            Route::get('/data-by-summary', 'data_by_summary')->middleware(['auth:sanctum', 'abilities:users']);
            Route::get('/current', 'current')->middleware(['auth:sanctum', 'abilities:users']);
        });
    });
});
