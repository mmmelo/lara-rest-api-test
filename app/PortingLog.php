<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PortingLog extends Model
{
    use Notifiable, HasApiTokens, SoftDeletes;

    protected $dates = array('deleted_at');

    protected $fillable = array(
        'id',
        'process_time',
        'success',
        'fail',
        'total',
        'user_id',
        'user_name',
        'created_at',
        'import_lot',
        'status',
    );

    protected $hidden = [
        'updated_at',

    ];
}
