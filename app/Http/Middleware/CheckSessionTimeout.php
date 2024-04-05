<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Sentinel::check() && time() - strtotime(session('last_activity')) > config('session.lifetime') * 60) {
            // dd(Sentinel::check());
            $role = Sentinel::check();
            $role = $role['role'];
            
            if($role = 'admin'){
                Sentinel::logout();
                return redirect('/adminlogin')->with('flashMessage', 'Your session has expired. Please log in again.');
            }else{
                Sentinel::logout();
                return redirect('/')->with('error', 'Your session has expired. Please log in again.');
            }
        }

        session(['last_activity' => now()]);

        return $next($request);
    }
}
