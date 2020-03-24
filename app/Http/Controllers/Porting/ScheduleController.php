<?php

namespace App\Http\Controllers\Porting;

use App\Http\Controllers\ApiController;
use App\MaxPrepsScores;
use App\PortingLog;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\PHP;

class ScheduleController extends ApiController
{

    public function __construct() {

    }

    public function import (Request $request, $id=false) {

        $data = array( 'imported' => array('success'=> 0, 'fails' => 0, 'duplicated'=>0,'total' => 0, 'no_season'=>0, 'no_event_date'=>0, 'sched_score'=>false));

        $ws_mygspn = new Client([
            'headers' => [ 'Content-Type' => 'application/x-www-form-urlencoded' ]
        ]);

        $scores = DB::table('max_preps_scores');

        if ( $id) {
            $scores->where('id', '=' , $id);
        } else {
            $scores->where('event_type', '=' , 2);
        }
        $scores->WhereRaw('(status = 0 or status is null)');
//        dd($scores->toSql(), $scores->getBindings());
        $scores =$scores->get();

        try {
            $lot = uniqid();
            foreach ( $scores as $i => $score) {
                $data['imported']['total']++;
                if (isset($score->home_school_id) && isset($score->away_school_id)) {

                    if( $this->check_dl_schedules($score->event_date, $score->home_school_id, $score->away_school_id)['data'] ) {

                        $home_team_season = $this->get_team_season( $score->home_school_id);
                        $opponent_team_season = $this->get_team_season( $score->away_school_id);

                        if (isset($home_team_season['data']) && isset($opponent_team_season['data']) ) {
                            $ev_date = explode("/", $score->event_date);

                            if( $ev_date[2] < 2018) {

                                $data['imported']['fails']++;
                                $data['imported']['no_event_date']++;
                                $max_scores = MaxPrepsScores::find($score->id);
                                $max_scores->updated_by = Auth::user()->id;
                                $max_scores->status = 5; // No event date properly
                                $max_scores->save();

                                continue;
                            }

                            if ( $id) {
                                $params = array(
                                    'event_date'        => $score->event_date,
                                    'event_name'        => $score->home_team_name,
                                    'event_time'        => $score->schedule_time,
                                    'game_date'         => $ev_date[1],
                                    'game_month'        => $ev_date[0],
                                    'game_year'         => $ev_date[2],
                                    'home_team_id'      => $score->home_school_id,
                                    'opponent_id'       => $score->away_school_id,
                                    'season_id_home'    => $home_team_season['data']->season_id,
                                    'season_id_opponent'=> $opponent_team_season['data']->season_id,
                                    'status'            => 1,
                                    'practice'          => false,
                                    'playoffs'          => false,
                                    'current_season_id' => $home_team_season['data']->season_id,
                                    'home_team_score'   => $score->home_score,
                                    'opponent_score'    => $score->away_score,

                                );
                            } else {
                                $params = array(
                                    'event_date'        => $score->event_date,
                                    'event_name'        => $score->home_team_name,
                                    'event_time'        => $score->schedule_time,
                                    'game_date'         => $ev_date[1],
                                    'game_month'        => $ev_date[0],
                                    'game_year'         => $ev_date[2],
                                    'home_team_id'      => $score->home_school_id,
                                    'opponent_id'       => $score->away_school_id,
                                    'season_id_home'    => $home_team_season['data']->season_id,
                                    'season_id_opponent'=> $opponent_team_season['data']->season_id,
                                    'status'            => 1,
                                    'practice'          => false,
                                    'playoffs'          => false,
                                    'current_season_id' => $home_team_season['data']->season_id,
                                );
                            }

                            $res = $ws_mygspn->request('POST',  env('MYGSPN_API_URL') . '/action/schedule',
                                ['body' => json_encode( $params)]
                            )->getBody()->getContents();
                            $result = $this->str_to_array($res);

                            $data['imported']['success']++;

                            // Only for Scores need to create a schedule
                            if ($id) {
                                return true;
                            } else {
                                $max_scores = MaxPrepsScores::find($score->id);
                                $max_scores->updated_by = Auth::user()->id;
                                $max_scores->status = 1;
                                $max_scores->approved = 1;
                                $max_scores->import_lot = $lot;
                                $max_scores->save();
                            }

                        } else {
                            $max_scores = MaxPrepsScores::find($score->id);
                            $max_scores->updated_by = Auth::user()->id;
                            $max_scores->status = 3;
                            $max_scores->import_lot = $lot;
                            $max_scores->save();

                            $data['imported']['fails']++;
                            $data['imported']['duplicated']++;
                        }
                    } else {

                        if ($id) {
                            return false;
                        } else {

                            $data['imported']['fails']++;
                            $data['imported']['no_season']++;
                            $max_scores = MaxPrepsScores::find($score->id);
                            $max_scores->updated_by = Auth::user()->id;
                            $max_scores->status = 4; // No Season
                            $max_scores->import_lot = $lot;
                            $max_scores->save();

                        }

                    }


                } else {
                    // Only for Scores need to create a schedule
                    if($id){
                        return false;
                    }

                    $max_scores = MaxPrepsScores::find($score->id);
                    $max_scores->updated_by = Auth::user()->id;
                    $max_scores->status = 2;
                    $max_scores->import_lot = $lot;
                    $max_scores->save();

                    $data['imported']['fails']++;
                }

            }

            if ( !$id) {
                $log = PortingLog::create( [
                    'success' => $data['imported']['success'],
                    'fail' => $data['imported']['fails'],
                    'total' => $data['imported']['success'] + $data['imported']['fails'],
                    'duplicates' => $data['imported']['duplicated'],
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                    'import_lot' => $lot,
                    'status'=> 2
                ])->save();


                $this->send_notify('fi_notify_dev', 'Porting: Schedule',
                    'Success: ' . $data['imported']['success'] . PHP_EOL
                    . 'fail: ' . $data['imported']['fails'] . PHP_EOL
                    . 'total: ' . ($data['imported']['success'] + $data['imported']['fails']) . PHP_EOL
                    . 'duplicates: ' . $data['imported']['duplicated']
                );
            }

        } catch ( \Exception $e) {
            echo $e->getTrace() . PHP_EOL;
            $data['error'] =  $e->getMessage();
        }

        return $this->printData($data);

    }

    public function str_to_array($json) {
        $data = array();

        if ($json) {
            $_json = json_decode($json, true);

            if ($json == json_encode($_json)) {
                $data = $_json;
            }
        }

        return $data;
    }

    private function get_team_season($group_id=false) {

        $data = array('error'=>false, 'data'=>array());
        try {
            $season = DB::table('seasons');
            $season->where('team_id', '=', $group_id);
            $season->orderBy('season_id', 'desc');
            $data['data'] = $season->first();
//            dd($season->toSql());
        } catch (\Exception $e) {
            $data['error'] = true;
            $data['description'] = $e->getMessage();
        }

        return $data;
    }

    private function check_dl_schedules($event_date, $home_id, $op_id) {

        try {
            $event = DB::table('group_events');
            $event->whereRaw("from_unixtime(event_date, '%m/%d/%Y') = '$event_date' ");
            $event->whereRaw("group_id = '$home_id' and  event_opponent_id='$op_id' ");

            $data['data'] = count($event->get())>0?false:true;
//            $data['data'] = $event->toSql();

        } catch ( \Exception $e) {
            $data['error'] = true;
            $data['description'] = $e->getMessage();
            $data['data'] = false;
        }

        return $data;

    }

}
