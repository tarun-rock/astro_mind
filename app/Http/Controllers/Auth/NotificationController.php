<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class NotificationController extends Controller
{
	public function rock()
	{
		return view('register');
	}

	public function register()
    {

    	return view('register');

    }
	public function insert(Request $request)
	{
		// dd($request->all());
		//$msg='row inserted';
		$user_created = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		
	}

}

