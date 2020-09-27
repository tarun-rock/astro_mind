<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class tag extends Model
{
    public function Posts()

    {
    	return $this->belongsToMany('App\Model\Post','post_tag');

    }
}