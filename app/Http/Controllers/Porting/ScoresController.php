<?php

namespace App\Http\Controllers\Porting;

use App\Http\Controllers\ApiController;
use App\MaxPrepsScores;
use App\PortingLog;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScoresController extends ApiController
{

    public function __construct()
    {
        ini_set('max_execution_time', '600');
    }

    /**
     * Display a listing of the resource.
     *ee
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->printData(MaxPrepsScores::all());
    }

    public function listScores(Request $request)
    {

        $limit = isset($request->limit) ? $request->limit : 10;
        $offset = isset($request->offset) ? $request->offset : 0;
//      $scores = MaxPrepsScores::take($limit)->skip($offset);

        $scores = MaxPrepsScores::whereRaw('status is null or status = 0');
        $scores->where('event_type', '=', 1);
        $scores->selectRaw("date_format(created_at, '%m/%d/%Y') as 'date'
                        ,count(created_at) as 'record' 
                        , state
                        , sport");
        $scores->groupBy(DB::raw("date_format(created_at, '%m/%d/%Y'), state, sport"));
        $scores->orderBy('date', 'desc');
        $scores->orderBy('state', 'asc');
        $scores->orderBy('sport', 'asc');
        $scores->orderBy('record', 'desc');
        $scores = $scores->get();

        return $this->printAll($scores);

    }

    public function import(Request $request)
    {

        $schudeleCtrl = new ScheduleController();



        $data = array('imported' => array('success' => 0, 'fails' => 0));
        $ws_mygspn = new Client([
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
        ]);

        $scores = DB::table('max_preps_scores');
//        $scores->whereRaw(' (home_team_slug is not null or away_team_slug is not null)');
        $scores->where('event_type', '=', 1);
//        $scores->where( 'state', '=', 'fl');
        $scores->where('status', '=', 0);
        $scores->orWhereRaw('status is null');
//        dd($scores->toSql());
        $scores = $scores->get();
//        echo print_r($scores); exit();
        $lot = uniqid();

        try {
            foreach ($scores as $i => $score) {

                $event_tb = DB::table('group_events');
                $event_tb->whereRaw("from_unixtime(event_date, '%m/%d/%Y') = '$score->event_date' ");
                if ($score->home_school_id) {
                    $event_tb->whereRaw("( group_id = $score->home_school_id or event_opponent_id = $score->home_school_id)");
                } elseif ($score->away_school_id) {
                    $event_tb->whereRaw("( group_id = $score->away_school_id or event_opponent_id = $score->away_school_id)");
                }
                $event = $event_tb->first();
                if (isset($event->group_id)) {

                    $event_update = DB::table('group_events');
                    $event_update->where('id', '=', $event->id);
                    $event_update->update([
                        'group_result' => $score->home_score,
                        'opponent_result' => $score->away_score,
                        'import_lot' => $lot,
                        'import' => true,
                    ]);

                    if ($score->home_school_id) {
                        $res = $ws_mygspn->request('POST', env('MYGSPN_API_URL') . '/calculate_group_win_loss',
                            ['body' => json_encode(['real_time_process' => true,
                                'home_team_id' => $event->group_id,
                                'season_id_home' => $event->season_id_home,
                                'opponent_id' => $event->event_opponent_id,
                                'season_id_opponent' => $event->season_id_opponent,
                                'is_opponent' => false,
                                ''])]
                        );

                    } elseif ($score->away_school_id) {

                        $res = $ws_mygspn->request('POST', env('MYGSPN_API_URL') . '/calculate_group_win_loss',
                            ['body' => json_encode(['real_time_process' => true,
                                'home_team_id' => $event->group_id,
                                'season_id_home' => $event->season_id_home,
                                'opponent_id' => $event->event_opponent_id,
                                'season_id_opponent' => $event->season_id_opponent,
                                'is_opponent' => true,])]
                        );
                    }

                    $max_scores = MaxPrepsScores::find($score->id);
                    $max_scores->updated_by = Auth::user()->id;
                    $max_scores->status = 1;
                    $max_scores->approved = 1;
                    $max_scores->import_lot = $lot;
                    $max_scores->save();

                    $data['imported']['success']++;

                } else {

                    $sched = $schudeleCtrl->import($request, $score->id);

                    if ($sched) {
                        $max_scores = MaxPrepsScores::find($score->id);
                        $max_scores->updated_by = Auth::user()->id;
                        $max_scores->status = 5; // Created Schedule and Score
                        $max_scores->import_lot = $lot;
                        $max_scores->save();
                        $data['imported']['success']++;
                    } else {
                        $max_scores = MaxPrepsScores::find($score->id);
                        $max_scores->updated_by = Auth::user()->id;
                        $max_scores->status = 2;
                        $max_scores->import_lot = $lot;
                        $max_scores->save();
                        $data['imported']['fails']++;
                    }


                }

            }

            $log = PortingLog::create([
                'success' => $data['imported']['success'],
                'fail' => $data['imported']['fails'],
                'total' => $data['imported']['success'] + $data['imported']['fails'],
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'import_lot' => $lot,
                'status' => 1
            ])->save();

            $this->send_notify('fi_notify_dev', 'Porting: Scores',
                'Success: ' . $data['imported']['success'] . PHP_EOL
                . 'fail: ' . $data['imported']['fails'] . PHP_EOL
                . 'total: ' . ($data['imported']['success'] + $data['imported']['fails'])

            );

            $data['imported']['total'] = $data['imported']['success'] + $data['imported']['fails'];

        } catch ( \Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            $data['error'] =  $e->getMessage();
        }


        return $this->printData($data);

    }

}
