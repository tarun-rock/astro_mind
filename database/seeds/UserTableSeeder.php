<?php

use Illuminate\Database\Seeder;
use App\user;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     	User::create([

     		'name' => 'rock',
     		'email' =>'tarun@1',
     		'password' => Hash::make('password'),

     	]);   
    }
}
