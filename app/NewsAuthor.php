<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class NewsAuthor extends Model
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'user_id','slug', 'permission', 'assoc_id',
    ];


    protected $hidden = [

    ];
}
