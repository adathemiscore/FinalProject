<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //user authentication
        
        if(Sentinel::check() && Sentinel::getUser()->roles()->first()->slug == 'admin'){
            // \Log::info('role', ['role' => Sentinel::getUser()->roles()->first()]);           
            return $next($request);
        }else{
            return redirect('/');
        }
        
        //authenticated user should have a role admin


    }
}
