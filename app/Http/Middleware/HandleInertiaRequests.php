<?php

namespace App\Http\Middleware;

use App\Helpers\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth.user' => Auth::user() ? array_merge($request->user()->only('name','email'), ['role' => $request->user()->role->code] ) : null,
            'route.name' => Route::currentRouteName(),
            'csrf_token' => csrf_token(),
            'cart' => [
                'items' => Cart::getCartItemsWithProducts(Auth::user()),
                'count' => Auth::user() ? Auth::user()->cartItems->sum('quantity') : Cart::getCartItemsCount(Auth::user()),
            ],
            'flash.message' => session('flash_message'),
        ]);
    }
}
