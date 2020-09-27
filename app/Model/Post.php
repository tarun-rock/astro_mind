<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    public function tags()
    {
    	return $this->belongsToMany('App\Model\tag','post_tags');
    }

    public function Category()
    {
    	return $this->belongsToMany('App\Model\Category','category_posts')->withTimestamps();;
    }

    
}
