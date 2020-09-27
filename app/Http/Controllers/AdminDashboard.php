<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\contact;
use App\Model\Category;
use App\Model\category_post;
use App\Model\Prod_Category;
use App\Model\Post;
use App\Model\Cat;
use App\Model\Product;
use App\Model\tag;
use Yajra\DataTables\DataTables;
use App\Repositories\Post\PostRepositoryInterface;
use DB;

class AdminDashboard extends Controller
{

    private $postRepository;
   
    public function __construct(PostRepositoryInterface $postRepository)
    {

        $this->postRepository = $postRepository;
        
    }

    public function adminDashboard()
    {
    	return view('admin.index');
    }

    public function contact(Request $request)
    {
    	if($request->ajax())
    	{
    		$getdata = contact::select('id', 'name','email','phone_no')->get();

            $data = Datatables::of($getdata)->make(true);

            return $data ;
    	}

    	return view ('admin.viewContact');
    }

	public function addPost(Request $request , $id = 0)
    {

    	if($request->isMethod('post'))
    	{
            
            if ($request->file('image')) {

                //dd('hello');
                $image = $request->file('image');

                $path = 'images/media';

               $imageName = $image->getClientOriginalName();

                //$ext = strtolower($image->getClientOriginalExtension());

           	    $rock = $image->move($path, $imageName);

                //$category = $request->category;

                $desc = $request->desc;
        		
                $ins_data = [

        			'title' => $request->title,
        			'slug' => $request->slug, 
        			'body' => $request->description,
                    'created_at' => date('Y-m-d H:i:s'),
                    


        		];

                //dd($ins_data);

                if(!empty($image))
                {

        			$ins_data['image'] = $rock;
                }


        		if(!empty($id) && $id != 0)
        		{
        			Post::where('id',$id)
    	            ->update([

    	            	$ins_data
    	            ]);

                    /*Category::where('id',$id)
                    ->update([
                        'name'=>$category
                    ]);*/
        		}	
        		else
        		{
        			$getId = Post::insert([

        				
                    'title' => $request->title,
                    'slug' => $request->slug, 
                    'body' => $request->description,
                    'image' => $rock,
                    'created_at' => date('Y-m-d H:i:s'),
                    'active' => 1,
                    'count' => 0
        			]);

                    //dd($getId);


        		}

        		return redirect()->route('post');
               
            }
        
    	}

    		if(!empty($id))
    		{

                $getData = Post::find($id);
        
                /*$getData = DB::table('posts')

                ->Join('category', 'posts.cat_id', '=', 'category.id')
                
                ->Join('media', 'posts.media_id', '=', 'media.id')
                
                ->select('posts.id','posts.title','posts.descprition', 'category.name','media.name as image')
                ->first();*/

    		}

            $category = $this->postRepository->category(); 

    	   return view('admin.AddPost')->with([

            'getData' => $getData ?? '',

    		'category' => $category['category'] ?? []

    	   ]);
    }

    public function viewPost(Request $request)
    {
    	if($request->ajax())
    	{

            //$getData = Post::all();

           $getData = DB::table('posts')->get();


    		/*$getData = DB::table('posts')

            ->Join('category', 'posts.cat_id', '=', 'category.id')
            
            ->Join('media', 'posts.media_id', '=', 'media.id')
            
            ->select('posts.id','posts.title','posts.descprition', 'category.name','media.name as image')
            ->get();*/

            //dd($getData);

            $data = Datatables::of($getData)->editColumn('image', function ($getData) {

                    if ($getData->image != null) {


                        $html = '<img src="' . $getData->image . '" width="150" class="img-fluid">';

                    }
                    else
                    {
                        $html = 'No Image Found';
                    }
                    return $html;

                })
                ->rawColumns(['image'])
                ->make(true);

            return $data;

    	}

    	return view('admin.viewPost');
    }

    public function viewCategory(Request $request)
    {
    	if($request->ajax())
    	{
    		$getData = Category::select('id','name','slug')->get();

    		$data = Datatables::of($getData)->make(true);

    		return $data ;
    	}

    	return view('admin.viewCategory');
    }

    public function addCategory(Request $request , $id = 0)
    {
    	if($request->isMethod('post')){

   
    		$ins_data = [

    			'name' => $request->category,

    			'slug' => $request->slug,

                'created_at' => date('Y-m-d H:i:s')
    		];

    		if (!empty($id) && $id != 0)
    		{ 

	    		Category::where('id',$id)
	            ->update([

	            	$ins_data
	           ]);
    		}
    		else
    		{
    			Category::insert([

    				$ins_data
    			]);
    		}

    		return redirect()->route('view-category');


    	}
    		if(!empty($id))
    		{
    			$getData = Category::select('id','name','slug')->first();
    		}

    	return view ('admin.AddCategory');
    }

    public function viewTag(Request $request)
    {
        if($request->ajax())
        {
            $getData = tag::select('id','name','slug')->get();

            $data = Datatables::of($getData)->make(true);

            return $data ;
        }

        return view('admin.viewTag');
    }

    public function addTag(Request $request , $id = 0)
    {
        if($request->isMethod('post')){

   
            $ins_data = [

                'name' => $request->tag,

                'slug' => $request->slug,

                'created_at' => date('Y-m-d H:i:s')
            ];

            if (!empty($id) && $id != 0)
            { 

                tag::where('id',$id)
                ->update([

                    $ins_data
               ]);
            }
            else
            {
                tag::insert([

                    $ins_data
                ]);
            }

            return redirect()->route('view-tag');


        }
            if(!empty($id))
            {
                $getData = tag::select('id','name','slug')->first();
            }

        return view ('admin.addTag');
    }

    public function viewProdCategory(Request $request)
    {
        if($request->ajax())
        {
            $getData = Prod_Category::select('id','name','slug')->get();

            $data = Datatables::of($getData)->make(true);

            return $data ;
        }

        return view('admin.viewProdCategory');
    }

    public function addProdCategory(Request $request , $id = 0)
    {
        if($request->isMethod('post')){

            $ins_data = [

                'name' => $request->category,

                'slug' => $request->slug,

                'created_at' => date('Y-m-d H:i:s')
            ];

            if (!empty($id) && $id != 0)
            { 

                Prod_Category::where('id',$id)
                ->update([

                    $ins_data
               ]);
            }
            else
            {
                Prod_Category::insert([

                    $ins_data
                ]);
            }

            return redirect()->route('view-prodCategory');


        }
            if(!empty($id))
            {
                $getData = Prod_Category::select('id','name','slug')->first();
            }

        return view ('admin.addProdCategory');
    }


    public function viewProduct(Request $request)
    {

        if($request->ajax())
        {
            $getData = Product::select('id','prod_title','prod_name','price','prod_image')->get();

            $data = Datatables::of($getData)->editColumn('prod_image', function($getData){

                if($getData->prod_image != null)
                {

                    $html = '<img src="' . $getData->prod_image . '" width="150" class="img-fluid">';

                }
                else
                {
                    $html = 'No Image Found';
                }

                return $html ;

            })
            ->rawColumns(['prod_image'])
            ->make(true);

            return $data ;
        }

        return view('admin.viewProduct');


    }

    public function addProduct(Request $request , $id = 0)
    {

        if($request->isMethod('post'))
        {
            
            if ($request->file('image')) {

                $image = $request->file('image');

                $path = 'images/media';

               $imageName = $image->getClientOriginalName();

                //$ext = strtolower($image->getClientOriginalExtension());

                $rock = $image->move($path, $imageName);

                $category = $request->category;
                
                $ins_data = [

                    'prod_title' => $request->title,
                    'prod_name' => $request->name, 
                    'price' => $request->price,
                    'cat_id' => $request->category,
                    'created_at' => date('Y-m-d H:i:s'),
                    

                ];

                //dd($ins_data);

                if(!empty($image))
                {

                    $ins_data['prod_image'] = $rock;
                }

                if(!empty($id) && $id != 0)
                {
                    Product::where('id',$id)
                    ->update([

                        $ins_data
                    ]);

                }   
                else
                {
                    Product::insert([
                        
                        $ins_data
                    ]);


                }

                return redirect()->route('view-product');
               
            }
        
        }

            if(!empty($id))
            {
                $getData = Product::find($id);
            }

            $prod_category = $this->postRepository->prodCategory();

            // dd($prod_category); 

           return view('admin.addProduct')->with([

            'getData' => $getData ?? '',

            'prod_category' => $prod_category['prod_category'] ?? []

           ]);
    }


}
