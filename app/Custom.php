<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Custom extends Model
{
    /**
     * @param string $season
     * @param int $limit
     * @return array
     */
    public static function get_schedule_season($state ,$sport, $shrink, $group_id)
    {
        $season = '2018 - 2019';
        $limit = 5000;

        if($shrink) {
            $schedule = DB::select("
            SELECT
              a.id,
              from_unixtime(a.event_date,'%M %D %Y @ %h:%i %p') as event_date,
              b.full_name as home_name,
              a.event_opponent_name,
              c.start_year,
              a.group_result,
              a.opponent_result,
              d.user_address_state,
              e.user_type_name
            FROM group_events a
                   INNER JOIN groups b on b.group_page_id = a.group_id
                   INNER JOIN seasons c on c.season_id = a.season_id_home
                   INNER JOIN user_loc d on b.group_page_id = d.user_id
                   INNER JOIN user_type_name e on a.type_id = e.user_type_id
            WHERE (a.group_id = :group_id OR a.event_opponent_id = :opponent_id)
            AND c.start_year= :schedule
            AND d.user_address_state = :state
            AND e.user_type_name = :sport
            AND a.event_date <= UNIX_TIMESTAMP()
        ORDER by a.event_date DESC
        ", ['schedule' => $season,'group_id' => $group_id ,'opponent_id' => $group_id, 'state' => $state, 'sport' => $sport]
            );
        }else{
            $schedule = DB::select("
        SELECT
        a.id,
               from_unixtime(a.event_date,'%M %D %Y @ %h:%i %p') as event_date,
               b.full_name as home_name,
               a.event_opponent_name,
               c.start_year,
               a.group_result,
               a.opponent_result,
               d.user_address_state,
               e.user_type_name
            FROM group_events a
              INNER JOIN groups b on b.group_page_id = a.group_id
              INNER JOIN seasons c on c.season_id = a.season_id_home
              INNER JOIN user_loc d on b.group_page_id = d.user_id
              INNER JOIN user_type_name e on a.type_id = e.user_type_id
            WHERE  c.start_year= :schedule
              AND d.user_address_state = :state
              and e.user_type_name = :sport
              AND a.event_date <= UNIX_TIMESTAMP()
            ORDER by a.event_date DESC
            LIMIT :limit
        ", ['schedule' => $season, 'limit' => $limit, 'state' => $state, 'sport' => $sport]
            );
        }
        return $schedule;
    }
}
