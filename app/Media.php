<?php

namespace App;

use App\Traits\Update_media_pivot;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    use Update_media_pivot;

    protected $fillable = [
        'bucket',
        'content_type',
        'size',
        'name',
        'ext',
        'user',
        'media',
        'mime_type',
        'thumb_id',
        'approved',
        'rating',
        'tags',
        'sell',
        'content_file_2'
    ];
    protected $table ='media';
//    public $timestamps = false;

    public function group(){
        return $this->belongsToMany('App\Group','media_relation','media_id','group_id');
    }

    public function tags() {
        return $this->belongsToMany('App\Tag_feeder','tag_media_pivot','media_id','tag_feed_id');
    }

    protected $dateFormat = 'U';
}
