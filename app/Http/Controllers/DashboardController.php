<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Model\contact;
use App\Model\Post;
use App\Model\Category;
use App\Model\Product;
use App\Model\post_tags;
use App\Model\tag;
use App\Model\Cart;
use App\Model\Status;
use Validator;
use DB;
use Notification;
use App\Notifications\MyFirstNotification;
use App\Repositories\Post\PostRepositoryInterface;
use Razorpay\Api\Api;

class DashboardController extends Controller
{

	private $adminView = "admin";

    private $postRepository;

     private $razorpayId = "rzp_test_htopM82D9l394s" ;

    private $razorpayKey = "SOO2xdogzskMDUqJvchKMB16" ;
   
    public function __construct(PostRepositoryInterface $postRepository)
    {

        $this->postRepository = $postRepository;
        
    }

	public function homePage()
    {
     	//$first = Post::get();

        $first = DB::table('posts')->paginate(4);
        //$cat = Post::get()->ta(4);

        return view('home')->with([

            	'first' => $first,
                //'cat' => $cat,
            	    	
        ]);
    }

    public function displaySinglePost(Request $request , $id) {

        $post =Post::find($id);
        $post->increment('count');

        $first = Post::limit(6)->get();    

    		/*$getData = DB::table('posts')

            ->Join('category', 'posts.cat_id', '=', 'category.id')
            
            ->Join('media', 'posts.media_id', '=', 'media.id')
            
            ->select('posts.id','posts.title','posts.descprition', 'category.name','media.name as image')
            
            ->where('posts.id','=',$id)

            ->first();*/
    	
    	return view('blogSingle')->with([

    		'post' => $post,
            'first' => $first

    	]);
	}

    public function search(Request $request)
    {

        if($request->isMethod('get'))
        {
            $term = $request->term;
            //dd($term);

            $getData = $this->postRepository->get($term);

            //dd($getData);
            return view('blog')->with([

                'getData' => $getData['getData'] 
            ]);
            
        }

    }

    
    public function category()

    {

        $category =Category::get();
       // dd($category);

        return view('layout.header')->with([
            
            'category' => $category

        ]);
    }


    public function contact(Request $request)
    {

    	if($request->isMethod('post')){

            /*$this->validate($request,[

			'email' => 'required|email|unique:contact-us'

			]);	*/


		 /*$this->validate($request,[

			//'name' => 'required|max:255',	

			'email' => 'required|email',

			'phone_no' =>'required|max:10|numeric'

		]);*/

		
		$getId = contact::insert([

			'name' => $request->name,
			'email' => $request->email,
			'phone_no' =>$request->phone,
			'querry' =>$request->message,
			'created_at' => date('Y-m-d H:i:s')
			//'id'=>1//'updated_at' => date('Y-m-d H:i:s')
		]);
			if($getId != '')
		
			{
			
				$data = contact::where('id',$getId)->select(['email',"name",'phone_no',"querry"])->first();

				$details =[

					'greeting' =>'Hi : ' .  $data->name,
					
					'body' => 'User query : ' . $data->querry,

					//'thanks' => 'this is the contact number :' . $data->phone_no,

					'id' => 1

				]; 
				Notification::route('mail','girishsharmaji@gmail.com')->notify(new MyFirstNotification($details));
			}
  		
		}
	
			return view("contact");

    }

    public function showProduct()
    {
        
        $first = DB::table('product')->paginate(4);

        return view('product')->with([

                'first' => $first,
                // 'cat' => $cat,
                        
        ]);
    }

    public function singleProduct(Request $request , $id)
    {
        
        $single = Product::find($id);

        $first = Product::all();


        return view('singleProduct')->with([

                'single' => $single,
                'first' => $first,
                        
        ]);
    }

    public function addCart(Request $request , $id = 0)
    {

        $user_id = \Auth::User()->id;

        $product_id = Product::find($id);

        /*$COUNT = Cart::select(DB::raw('count(product_id) as PRODUCTS'))
    
        ->where('user_id' , '=' ,$user_id)
        
        ->get();

        dd($COUNT);*/


        $cart = Cart::select('product_id')
                ->where('product_id', '=', $id)
                ->first();

        if(empty($cart))
        {

            $data = Cart::insertGetId([

                'user_id'    => $user_id,

                'product_id' => $product_id->id

            ]);

            return $data;
        }
        /*else
        {
            return 0 ;
        }*/
        




        /*$cart = DB::table('cart')

            ->Join('product', 'cart.product_id', '=', 'product.id')
            
            ->Join('prod_category', 'product.cat_id', '=', 'prod_category.id')
            
            ->select(
                'product.prod_title as prod_title',
                'product.prod_name as prod_name',
                'product.prod_image as prod_image',
                'product.price as price',
                'prod_category.name as name'
            )
            ->where('cart.id', '=', $data)
            ->first();*/

        /*$cart = getTableData(Cart::class ,[

            "select" =>[
                'cart.*',
                'product.prod_title',
                'product.prod_name',
                'product.prod_image',
                'product.price',
                'prod_category.name',
            ],
            "joins" =>[

                [
                    "table" => "product",
                    'type' => returnConfig("left_join"),
                    "left_condition" => "product.id",
                    "right_condition" => "cart.product_id"
                ],

                    "table" => "prod_category",
                    'type' => returnConfig("left_join"),
                    "left_condition" => "prod_category.id",
                    "right_condition" => "product.cat_id"
                ],

            ],
            
            "where" => [

                'cart.id' => $id
            ]

        ]);*/

        
    }

    public function viewCart()
    {

        $first = Product::all();

        $user_id = \Auth::User()->id;

        $COUNT = Cart::select(DB::raw('count(product_id) as PRODUCTS'))
    
        ->where('user_id' , '=' ,$user_id)

        ->get();



        /*$cart = DB::table('cart')

            ->Join('product', 'cart.product_id', '=', 'product.id')
            
            ->Join('prod_category', 'product.cat_id', '=', 'prod_category.id')
            
            ->select(
                'product.prod_title as prod_title',
                'product.prod_name as prod_name',
                'product.prod_image as prod_image',
                'product.price as price',
                'prod_category.name as name'
            )
            ->where('cart.user_id', '=', $user_id)
            ->get();*/

        return view('cart')->with([

            // 'cart' => $cart,
            'first' => $first
        ]);

    }

    public function initiate()
    {
        return view('payment-initiate');
    }

    public function payment(Request $request)
    {
        $api = new Api($this->razorpayId, $this->razorpayKey);

        $receiptId = Str::random(20);

        $order = $api->order->create(array(
                'receipt' => $receiptId,
                'amount' => $request->amount,
                'payment_capture' => 1,
                'currency' => 'INR'
            )
        );

        $response = [
            'orderId' => $order['id'],
            'razorpayId' => $this->razorpayId,
            'amount' =>$request->amount * 100,
            "name"  => $request->name,
            'currency' => 'INR',
            'email' => $request->email,
            'contactNumber' => $request->contact,
            'address' =>$request->address,
            'description' =>'test mode'
        ];

        return view('payment')->with([

            'response' => $response,

        ]); 

    }
    private function signatureVerify($_signature , $_paymentId , $_orderId)
    {
        try
        {
            $api = new Api($this->razorpayId, $this->razorpayKey);

            $attributes  = array('razorpay_signature'  => $_signature,  'razorpay_payment_id'  => $_paymentId ,  'razorpay_order_id' => $_orderId);
            
            $order  = $api->utility->verifyPaymentSignature($attributes);

            return true;

        }
        catch(\Exception $e)
        {
            return false;
        }
    }
    public function complete(Request $request)
    {
        $signatureStatus = $this->signatureVerify(

            $request->all()['rzp_signature'],
            $request->all()['rzp_paymentid'],
            $request->all()['rzp_orderid']
        );

        if($signatureStatus == true)
        {
            return view('thankyou');
        }else{
            
            return view('payment-failed');

        }
    }

    public function test()
    {
        return view("login-model");
    } 

    /*public function test()
    {
        $data = "India";

        $country = countries::select('name' , DB::raw(
                        "CONCAT('[', GROUP_CONCAT(
                            JSON_OBJECT(
                            
                                'ids', id
                            )
                        ), ']') as ids"
                    ),)->groupBy('name')
                    ->where('name' ,'=', $data)
                    ->get();

        $rock = $country->pluck('ids', 'name');
        
        $country->map(function ($value) {

        
                    $value->ids = collect(json_decode($value->ids));
        

                return $value;
            });
        dd($country);
    
        $data = DB::table('users')->select('id','country_id')->whereIn('country_id', [ 2, 3])->get();

        dd($data);
    }*/
    
}