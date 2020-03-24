<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sports_type extends Model
{
    public $table = 'user_type_name';
    public $primaryKey = 'user_type_id';
    public $timestamps = false;

    public function tags()
    {
        return $this->belongsToMany('App\Tag_feeder','tag_feed_pivot','user_type_name_id','tag_feed_id');
    }
}
