<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;


class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*$userRoles = auth::user()->roles->pluck('name');

        if(!$userRoles->contains('admin')){

            return redirect('login');

        }
            return $next($request);*/

            if (!Auth::check()) {

            return $next($request);

        }


        if(\Auth::User()->type == 1)
        {
            dd("hello");
            return $next($request);
        }
        else
        {
            dd("hello");
            return route('dashboard');
        }
    }
}
