<?php

use App\Http\Controllers\Api\V1\AmazonProductController;
use App\Http\Controllers\Api\V1\CalculatorController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\UserLoginController;
use App\Http\Controllers\Api\V1\UserRegisterController;
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
        Route::post('payment-index', [PaymentController::class, 'index']);
        Route::get('product-show/{asin}', [ProductController::class, 'show'])->name('product-show');
        Route::post('amazon-url', [AmazonProductController::class, 'url']);
        Route::post('cart-store', [CartController::class, 'store']);

        Route::post('ticket-index', [TicketController::class, 'index']);
        Route::get('ticket-create', [TicketController::class, 'create']);
        Route::post('ticket-store', [TicketController::class, 'store']);
        Route::post('invoice-index', [InvoiceController::class, 'index']);

        Route::get('calculator-index', [CalculatorController::class, 'index']);
        Route::post('calculator-show', [CalculatorController::class, 'show']);

        Route::post('cart-index', [CartController::class, 'index']);

    });

});
