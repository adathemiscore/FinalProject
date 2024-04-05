<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class VisitorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Sentinel::check()){
            return $next($request);
        }else{
            if(Sentinel::getUser()->roles()->first()->slug == 'seller'){
                return redirect('/seller');
            }elseif(Sentinel::getUser()->roles()->first()->slug == 'buyer'){
                return redirect('/buyer');
            }else{
                return redirect('/home');
            }
        }
    }
}
