<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class MaxPrepsScores extends Model
{
    use Notifiable, HasApiTokens, SoftDeletes;

    protected $dates = array('deleted_at');

    protected $fillable = array(
        'id',
        'home_team_name',
        'home_team_max_preps_id',
        'home_team_slug',
        'away_team_name',
        'away_team_max_preps_id',
        'away_team_slug',
        'home_school_id',
        'away_school_id',
        'event_date',
        'state',
        'sport',
        'gender',
        'home_score',
        'away_score',
        'approved',
        'deleted',
        'event_type',
        'schedule_time',
        'import_lot',

    );

    protected $hidden = [
        'updated_at',
        'created_at'
    ];


}
