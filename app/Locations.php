<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    public $table = 'user_loc';
    public $primaryKey = 'user_id';
    public $timestamps = false;

    public function address()
    {
        return $this->belongsTo('App\Group','group_page_id');
    }
}
