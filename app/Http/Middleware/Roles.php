<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Roles
{
    /**
     * Handle an incoming request for specific user`s role.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if(!$request->user()->hasRole($roles)){ // if user dont have this roles
            return redirect()->route('home')->with( 'flash_message', [ 'warning' => 'You don`t have permission to access the route.' ] );
        }
        return $next($request);
    }
}
