<?php

use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\UserLoginController;
use App\Http\Controllers\Api\V1\UserRegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::prefix('v1/')->namespace('api/v1/')->group(function () {
    Route::post('register-set-number', [UserRegisterController::class, 'setNumber']);
    Route::post('register-validate-otp', [UserRegisterController::class, 'validateOtp']);
    Route::post('send-otp', [UserLoginController::class, 'getNumber'])->middleware('throttle:login');
    Route::post('validate-otp', [UserLoginController::class, 'validateOTP'])->middleware('throttle:login');

    Route::middleware(['auth:api'])->group(function () {
        Route::post('register-form', [UserRegisterController::class, 'registerForm']);
        Route::post('logout', [UserLoginController::class, 'logout']);
        Route::get('profile', [ProfileController::class, 'profile']);



        Route::get('payment-list', [PaymentController::class, 'list']);


    });

});
