<?php


namespace App\Repositories\Post;


use App\Model\Post;
use App\Model\Media;


class PostRepository implements PostRepositoryInterface
{        

  
    public function uploadFiles($file, $path, $extra = [])
    {

        if (!empty($file)) {

            $destination = base_path("public/" . $path);

            $ext = strtolower($file->getClientOriginalExtension());

            //$name = Str::random(2) . "_" . time() . "." . $ext;

            $file->move($destination);

            $links = url($path);

            $response = ["path" => $links, "file" => $destination];

            if (empty($extra["na"])) {
                $insert = [
                    "name" => $links,
                    "active" => 1,
                    "created_at" => date('Y-m-d H:i:s')
                ];

                $extra = [
                    "data" => $insert,
                    //"id" => 1
                ];

                $mediaID = Media::insertGetId([

                    $extra
                ]);

                $response["media_id"] = $mediaID;

            }

            return $response;

        }
    }

   
    public function category()
    {

        $category = \App\Model\Category::select('id as value','name')->get();
       
        return ["category" => $category];
    }

     public function prodCategory()
    {

        $prod_category = \App\Model\Prod_Category::select('id as value','name')->get();
       
        return ["prod_category" => $prod_category];
    }


    public function get($term)
    {

        $getData = \App\Model\Post::select(['id',"title",'body',"image"])

        ->where('body', 'like', '%' . $term . '%')
        ->orWhere('title', 'like', '%' . $term . '%')->get();

        //dd($getData);

        return ["getData" => $getData];

    }

}
