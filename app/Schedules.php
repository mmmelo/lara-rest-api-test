<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Schedules extends Model
/** Named also as the EVENT calendar
 * The Database uses is the group_events
 */
{
    protected $table = 'group_events';
    public $timestamps = false;

    // This would pull the information from the Home
    public function home(){
        return  $this->belongsTo('App\Group','group_id','group_page_id');
    }

    //This would pull the information from the opponent
    public function opponent()
    {
        return  $this->belongsTo('App\Group','event_opponent_id','group_page_id');
    }

    public function seasons()
    {
        return $this->belongsTo('App\Season','season_id_home','season_id');
    }

    public function media()
    {
        return $this->belongsToMany('App\Media','media_relation','events_id','media_id');
    }

    public function sport_type()
    {
        return $this->hasOne('App\Sports_type','user_type_id','type_id');
    }
}
