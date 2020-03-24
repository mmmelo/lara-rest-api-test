<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class States extends Model
{

    use Notifiable, HasApiTokens;

    public $table = 'states';
    public $primaryKey = "state_id";

    protected $fillable = [
        'state_id', 'country_id', 'state_code','state_name'
    ];
}
