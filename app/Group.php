<?php

namespace App;

use App\Traits\Update_media_pivot;
use Illuminate\Database\Eloquent\Model;
use App\Media;

class Group extends Model
{
    use Update_media_pivot;
    public $table = 'groups';
    public $primaryKey = 'group_page_id';
    public $timestamps = false;

    public function media(){
        return $this
            ->belongsToMany(Media::class,'media_relation','group_id','media_id')
            ->whereNull('deleted_at');
    }

    public function schedules(){
        return $this->hasMany('App\Schedules','group_id','group_page_id');
    }

    public function address()
    {
        return $this->hasOne('App\Locations','user_id');
    }

    public function players()
    {
        return $this->hasOne('App\Players','id');
    }

    public function media_logo()
    {
        return $this->hasOne('App\Players_files','player_id','group_page_id');
    }


}
