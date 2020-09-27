<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Notification;
use App\Notifications\MyFirstNotification;

class HomeController extends Controller
{
  
    public function sendNotification()
    {
        $user = User::first();
  
        $details = [
            'greeting' => 'Hi tarun',
            'body' => 'This is my first notification ',
            'thanks' => 'Thank you for using this',
            'actionText' => 'View My Site',
            'actionURL' => url('/'),
            'id' => 26
        ];
  
        Notification::send($user, new MyFirstNotification($details));
   
        dd('done');

        return view('home')->with([
            
            'details'=>$details
        ]);
    }

    public function email()
    {

        $details = [
            'title' => 'Mail from Tarun sharma',
            'body' => 'This is for testing email using smtp'
        ];
       
        \Mail::to('increadibletarun07@gmail.com')->send(new \App\Mail\MyTestMail($details));

       
        dd("Email is Sent.");

    }

    

}
