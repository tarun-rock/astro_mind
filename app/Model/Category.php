<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    public function Posts()

    {
    	return $this->belongsToMany('App\Model\Post','category_posts');

    }
}
