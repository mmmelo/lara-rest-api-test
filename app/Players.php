<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Players extends Model
{
    public $table = 'players';
    public $primaryKey = 'id';
    public $timestamps = false;
}
