<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\Checkout;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Profiler\Profile;

// routes for guest or authenticated
Route::get('/',[HomeController::class,'index'])->name('home');
Route::get('/product/{product:slug}',[HomeController::class,'view'])->name('product.view');

// guest routes
Route::controller(AuthController::class)->middleware('guest')->group(function(){
    Route::inertia('/register','Auth/Register')->name('register');
    Route::inertia('/login','Auth/Login')->name('login');
    Route::post('/register','register');
    Route::post('/login','login');
});

// authenticated routes
Route::middleware('auth')->group(function(){
    Route::inertia('/verify-email','Auth/VerifyEmail')->name('verify-email')->middleware('unverified');
    Route::post('/verify-email', [EmailController::class,'verifyEmail'] )->middleware('unverified');   
    //route when user click the email verifiation link that was sent to their email
    Route::get('/email/verify/{id}/{hash}',[EmailController::class,'emailVerification'])->name('verification.verify')->middleware('signed');
    Route::post('/logout',[AuthController::class,'logout'])->name('logout');
    
});

// cart routes for anyone
Route::prefix('/cart')->controller(CartItemController::class)->group(function(){
    Route::inertia('/','Cart')->name('cart.index');
    Route::post('/add/{product}','add')->name('cart.add');
    Route::delete('/remove/{product}','remove')->name('cart.remove');
    Route::put('/update/{product}','update')->name('cart.update');
});


// checkout for guest
Route::controller(CheckoutController::class)->middleware('guest')->group(function(){
    Route::get('/checkout/form','checkoutForm')->name('checkout.form');
    Route::post('/checkout/register','checkoutRegister')->name('checkout.register');
});

// checkout for authenticated
Route::controller(CheckoutController::class)->middleware('checkout','auth')->group(function(){
    Route::post('/checkout','checkout')->name('checkout');
    Route::post('/checkout/{order}','checkoutOrder')->name('checkout.order');
    Route::get('/checkout/success/{session_id?}','success')->name('checkout.success');
    Route::get('/checkout/failed/{session_id?}','failure')->name('checkout.failure');
});

Route::controller(OrderController::class)->middleware('auth')->group(function(){
    Route::get('/orders','index')->name('orders');
    Route::get('/orders/{order}','view')->name('orders.view');
});

// only profile routes needs to have verified authentication to access 
Route::controller(ProfileController::class)->middleware('auth','verified')->group(function(){
    Route::get('/profile','index')->name('profile');
    Route::put('/profile/{user}','update')->name('profile.update');
});

// Admin access
Route::resource('/products',ProductController::class)->middleware(['auth','verified','roles:admin']);

// stripe webhook
Route::post('/webhook/stripe', [CheckoutController::class, 'webhook']);
