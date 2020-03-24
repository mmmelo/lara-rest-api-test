<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag_feeder extends Model
{
    public $table = 'tag_feed';
    protected $fillable = ['description, count'];

    public function sports() {
        return $this->belongsToMany('App\Sports_type','tag_feed_pivot','tag_feed_id','user_type_name_id');
    }

    public function media() {
        return $this->belongsToMany('App\Media','tag_media_pivot','tag_feed_id','media_id');
    }

}
