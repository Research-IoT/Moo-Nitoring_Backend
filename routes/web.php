<?php

use App\Helpers\ApiHelpers;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/api/v1');
});

Route::get('/login', function () {
    return ApiHelpers::badRequest([], 'Belum Login', 401);
})->name('login');
