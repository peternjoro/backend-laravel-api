<?php

#use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\UserSettingsController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

//Route::get('all-items', [UserSettingsController::class, 'index']);

Route::middleware('jwt.verify')->group(function() {
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'getUser']);

    Route::controller(UserSettingsController::class)->group(function(){
        Route::get('user-settings','index');
        Route::post('user-settings','store');
        Route::get('user-settings/{id}', 'show');
        Route::post('user-settings/{id}', 'update');
        Route::delete('user-settings/{id}', 'destroy');
    });
});
