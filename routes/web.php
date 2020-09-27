<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('home');


});
*/
Auth::routes();

Route::group(['middleware' => ['auth']], function () {

	//Route::get('main','LoginCOntroller@index')->name('login');
	

Route::group(['middleware' => ['admin']], function () {


	// Route::any('dashboard','AdminDashboard@adminDashboard')->name('dashboard');
	//Route::get('main/successlogin','LoginCOntroller@successlogin')->name('success');

});

});


	Route::get('main/logout','LoginCOntroller@logout');


$dash = "DashboardController";

				/*---login---*/

Route::get('main','LoginCOntroller@index')->name('login');

Route::any('main/checklogin','LoginCOntroller@checklogin')->name('checklogin');
Route::get('main/successlogin','LoginCOntroller@successlogin')->name('success');

Route::any('logout','LoginCOntroller@logout')->name('logout');
Route::any('reset','LoginCOntroller@reset')->name('reset');


Route::any('test',"$dash@test")->name('test');



		/*-------register------*/


Route::any('registeration','LoginCOntroller@insert')->name('register');


           /*------notifications------*/

Route::get('send', 'HomeController@sendNotification');

Route::get('email',"HomeController@email");


			/* -----astromind----*/


 Route::any('home',"$dash@homePage")->name('home');

 //Route::any('model',"$dash@loginModel")->name('login-model');

 Route::any('contact-us',"$dash@contact")->name('contact-us');
 Route::any('blog',"$dash@blog")->name('blog');

 Route::any('post/{id}',"$dash@displaySinglePost")->name('single');

 Route::get('search',"$dash@search")->name('search');

 Route::get('category',"$dash@category")->name('category');


 					/* ---products---*/

 Route::get('product',"$dash@showProduct")->name('product');

 Route::get('single-product/{id}',"$dash@singleProduct")->name('single-product');


 			/*-----Add To Cart------*/

 Route::any('cart/{id}',"$dash@addCart")->name('cart');

 Route::get('view-cart',"$dash@viewCart")->name('view-cart');

 Route::any('model',"$dash@loginModel");

 		/*-------captcha route-------*/

Route::get('createcaptcha', 'CaptchaController@create');
Route::post('captcha', 'CaptchaController@captchaValidate');
Route::get('refreshcaptcha', 'CaptchaController@refreshCaptcha');


 	/*----------Admin panel--------*/

	Route::any('dashboard','AdminDashboard@adminDashboard')->name('dashboard');
	Route::any('view-contact','AdminDashboard@contact')->name('contact');
	
	Route::any('add-category','AdminDashboard@addCategory')->name('add-category');
	Route::any('view-category','AdminDashboard@viewCategory')->name('view-category');

	Route::any('add-tag','AdminDashboard@addTag')->name('add-tag');
	Route::any('view-tag','AdminDashboard@viewTag')->name('view-tag');
	
	Route::any('add-post','AdminDashboard@addPost')->name('view-post');
	Route::any('view-post','AdminDashboard@viewPost')->name('post');
	
			/*-------product--------*/
	Route::any('add-product','AdminDashboard@addProduct')->name('add-product');		
	Route::any('view-product','AdminDashboard@viewProduct')->name('view-product');

	Route::any('add-prodcategory','AdminDashboard@addProdCategory')->name('add-prodCategory');
	Route::any('view-prodcategory','AdminDashboard@viewProdCategory')->name('view-prodCategory');
		

		/*---payment gateway---*/

	Route::get('pay', 'RazorpayController@index');
  	Route::post('paysuccess', 'RazorpayController@paysuccess');
  	Route::post('razor-thank-you', 'RazorpayController@thankYou');	
  	

  	Route::get("initiate", "$dash@initiate");	
  	Route::post("payment-request-initiate", "$dash@payment");	
  	Route::any("complete", "$dash@complete");	