<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use App\User;
use Notification;
use App\Notifications\MyFirstNotification;

class LoginCOntroller extends Controller
{
	// protected $redirectTo = 'main/successlogin';


	public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    protected function redirectTo()
    {
        if(Auth::User()->type == 1)
        {
            dd('hy');
            return 'admin';
        }
        else
        {
            dd('hyss');
            return 'main';
        }

    }

	public function insert(Request $request)
	{

		if($request->ismethod('post')){

		$data = User::insert([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
			//'phone_no' => $request->phone,
			'type' => $request->type,
			'created_at' => date('Y-m-d H:i:s'),
			//'updated_at' => date('Y-m-d H:i:s')
		]);


			return redirect()->route('login');
			
		}

			return view("registeration");

	}


	public function index()
	{


   		 return view('login');

	}

	public function checklogin(Request $request)
	{
		/*$this->validate($request,[

			'email' => 'required|email|user',

			'password' => 'required|alphaNum|min:3'

		]);*/
		//dd(":hello");

		$user_data = array(

			"email" => $request->get('email'),
			"password" => $request->get('password'),

		);
		if (Auth::attempt($user_data)) {
		
			return redirect('main/successlogin');
		}

		else {
			
			return back()->with('error','Wrong login detail');
		}


		/*print_r($user_data);
		die;*/


		/*if(Auth::attempt($user_data))
		{

    		$id = \Auth::User()->id;

    		$data = User::where('id',$id)->select(['email'])->first();

			$user = User::first();	

			$details =[

				'greeting' =>'Hy tarun',
				
				'body' => "you are successfully login",

				'id' => $id

			]; 

			Notification::route('mail',$data->email)->notify(new MyFirstNotification($details));

			return redirect('main/successlogin');	

		}
		else
		{
			return back()->with('error','Wrong login detail');
		}*/

	}
	public function successlogin()
	{
		return view ('successlogin');
	}

	public function logout()
	{
		Auth::logout();

		return  redirect('main');
	}

	public function userDemo()
	{
		

		return  view('user');
	}

	public function adminDemo()
	{
		

		return  view('dashboard');
	}

	public function reset()
	{
		
	}



}
