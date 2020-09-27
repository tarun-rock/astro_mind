<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        //dd("hello");
        //dd($request->all());
        if (! $request->expectsJson()) {
            //dd(!$request->expectsJson());
            // $id = \Auth::User()->id;
           // dd($id);
            
            /*if(\Auth::User()->type == 1)
            {
                dd('hy');
                return route('dashboard');
            }
        else
            {
                dd('hyss');
                return route('success');
            }*/
            //return redirect()->route('checklogin');
          // return redirect('login');
        }
    }

    /*protected function redirectTo($request)
    {

        if(\Auth::user()->id == 'rock')
            {
                dd('hy');
                return 'dashboard';
            }
        else
            {
                dd('hyss');
                return 'main';
            }

    }*/
}
