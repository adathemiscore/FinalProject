<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class BuyerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         
        if(Sentinel::check() && Sentinel::getUser()->roles()->first()->slug == 'buyer'){
            // \Log::info('role', ['role' => Sentinel::getUser()->roles()->first()]);           
            return $next($request);
        }else{
            return redirect('/');
        }
    }
}
