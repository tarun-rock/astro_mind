<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';


    public function views()
    {
        return $this->morphMany(
            \App\Model\View::class,
            'viewable'
        );
        
    }

    public function getViewsCount() //total number of views
    {
        return $this->views()->count();
    }

    public function getViewsCountSince($sinceDateTime)
    {
        return $this->views()->where('created_at', '>', $sinceDateTime)->count();
    }

     public function getViewsCountUpto($uptoDateTime)
    {
        return $this->views()->where('created_at', '<', $uptoDateTime)->count();
    }

    	/* total number of views
		$post->getViews();*/

		/*// total number of views since the past 24 hours
		$post->getViewsCountSince(Carbon::now()->subDay(1));
*/
		/*// total number of views upto 2 months ago
		$post->getViewsCountUpto(Carbon::now()->subMonths(2));*/

}