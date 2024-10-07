<?php

namespace App\Providers;

use App\Contracts\CartServiceInterface;
use App\Services\CartService;
use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartServiceInterface::class, CartService::class );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // set stripe secret key from config/services.php
        Stripe::setApiKey( config('services.stripe.secret') );
    }
}
