<?php

use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AmazonProductController;
use App\Http\Controllers\Api\V1\ArticlesCatergoryController;
use App\Http\Controllers\Api\V1\ArticlesController;
use App\Http\Controllers\Api\V1\CalculatorController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\DashbboardController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\UserLoginController;
use App\Http\Controllers\Api\V1\UserRegisterController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Middleware\UserRegisterComplete;
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
    Route::post('send-otp', [UserLoginController::class, 'getNumber'])->middleware('throttle:login')->name('login');
    Route::post('validate-otp', [UserLoginController::class, 'validateOTP'])->middleware('throttle:login');
    Route::post('news', [NewsController::class, 'add']);

    Route::middleware(['auth:api'])->group(function () {
        Route::post('register-form', [UserRegisterController::class, 'registerForm']);
        Route::post('logout', [UserLoginController::class, 'logout']);
        Route::get('profile', [ProfileController::class, 'profile']);
        Route::put('profile-update', [ProfileController::class, 'update']);

        Route::post('payment-index', [PaymentController::class, 'index']);

        Route::get('product-show/{asin}', [ProductController::class, 'show'])->name('product-show');

        Route::post('ticket-index', [TicketController::class, 'index']);
        Route::get('ticket-create', [TicketController::class, 'create']);
        Route::post('ticket-store', [TicketController::class, 'store']);
        Route::post('ticket-reply', [TicketController::class, 'reply']);
        Route::get('ticket-show/{code}', [TicketController::class, 'show']);

        Route::post('invoice-index', [InvoiceController::class, 'index']);
        Route::post('invoice-show/{code}', [InvoiceController::class, 'show']);

        Route::get('calculator-index', [CalculatorController::class, 'index']);
        Route::post('calculator-show', [CalculatorController::class, 'show']);

        Route::post('cart-index', [CartController::class, 'index']);
        Route::post('cart-store', [CartController::class, 'store'])->middleware(UserRegisterComplete::class);

        Route::post('wallet-charge', [WalletController::class, 'walletCharge']);

        Route::get('address-index', [AddressController::class, 'index']);
        Route::post('address-store', [AddressController::class, 'store']);
        Route::put('address-update/{id}', [AddressController::class, 'update']);

        Route::get('dashboard-index', [DashbboardController::class, 'index']);

    });

    Route::get('wallet-charge-callback-zarinpal', [WalletController::class, 'callbackZarinpal']);

    Route::get('articles', [ArticlesController::class, 'index']);
    Route::get('article-category', [ArticlesCatergoryController::class, 'index']);
    Route::post('category-show', [ArticlesCatergoryController::class, 'show']);
    Route::get('article-show/{slug}', [ArticlesController::class, 'show']);
    Route::post('article-future', [ArticlesController::class, 'future']);
    Route::post('article-mostview', [ArticlesController::class, 'mostview']);

    Route::post('amazon-url', [AmazonProductController::class, 'url']);

});
