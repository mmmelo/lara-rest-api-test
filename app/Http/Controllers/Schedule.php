<?php

namespace App\Http\Controllers;

use App\Schedules;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Custom;



class Schedule extends Controller
{
    public function index()
    {
        $data = Schedules::with(['home','opponent','seasons'])
            ->whereRaw('(group_result AND opponent_result)is NULL')
            ->take(500)
            ->get();
        return $data;
    }

    public function find($id)
    {
        $data = Schedules::with(['home','opponent','media' => function($query)
        {$query->groupBy('id');}])
            ->where('id',$id)
            ->firstOrFail();
        return $data;
    }

    public function find_v2(Request $request)
    {
        $id = $request->id;
        $data = Schedules::with(['home','opponent','media' => function($query)
        {$query->groupBy('id');}])
            ->where('id',$id)
            ->firstOrFail();
        return $data;
    }

    public function find_schedule(Request $request)
    {
        $state = $request->state;
        $sport = $request->sport;
        $shrink =  $request->shrink;
        $group_id = $request->group_id;

//        echo($state); exit;
        $data = Custom::get_schedule_season($state,$sport,$shrink,$group_id);

        return $data;
    }

    public function media_added($id)
    {
        $data = Schedules::with(['sport_type.tags','home.address','home.media_logo','opponent.address','opponent.media_logo','media' => function($query)
        {
            $query->groupBy('id');
        }])
            ->where('id',$id)
            ->firstOrFail();

        //This portion is an example of having roster to show on the confirmation.

        $team_A =
        [
            'Ber Laurie',
            'Leon Yuryaev',
            'Marci Springthorp',
            'Nessy Dachey',
            'Shellysheldon Hargie',
            'Mariel Godson',
            'Rooney Cestard',
            'Debi Clyde',
            'Jean McOmish',
            'Rowan Watsam'
        ];

        $team_B =
            [
                'Creigh Sands-Allan',
                'Cristy Beauman',
                'Charity Ville',
                'Cleon Souttar',
                'Antonina Gegay',
                'Elston Thornebarrow',
                'Veriee Monini',
                'Orazio Longridge',
                'Skip Radbond',
                'Tricia Garment'
            ];

        $tag = array();

        /* This is the Tag initial "Hard Bounding proposal
        This would help the user to show the tags are already added to our system
        */

        //Collection of initial Tag Home and Opponent

        $teams = ['home','opponent'];
        $options = ['type','group_level','group_gender','full_name'];

        foreach ($teams as $team)
        {
            foreach ($options as $option)
            {
                if(!in_array($data->$team->$option,$tag))
                {
                    $tag[] = $data->$team->$option;
                }
            }
        }

        //Adding address and locations into the tags

        $address_fields = ['user_address_city','user_address_state','user_address_zip'];

        foreach ($teams as $team)
        {
            foreach ($address_fields as $address)
            {
                if(!in_array($data->$team->address->$address,$tag))
                {
                    $tag[] = $data->$team->address->$address;
                }
            }
        }

        //Adding Dynamic Tags

        $super_tag = array();
        $intelligent_tags =  $data->sport_type->tags;
        foreach ($intelligent_tags as $intelligent_tag)
        {
            $super_tag[] = $intelligent_tag;
        }

        //adding the school names in Regex Option

        $school_regex['home_regex'] = $this->regex_high_school($data['home']['full_name']);
        $school_regex['opponent_regex'] = $this->regex_high_school($data['opponent']['full_name']);

//        return [$data];
        return view('confirmation_drop')
            ->with('data',$data)
            ->with('school_regex',$school_regex)
            ->with('hard_tags',$tag)
            ->with('team_a',$team_A)
            ->with('team_b',$team_B)
            ->with('super_tags',$super_tag);
    }

    public function save_score(Request $request)
    {
        $id = $request->id;
        $home = $request->home;
        $away = $request->away;

        $ws_mygspn = new Client([
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
        ]);

        //update the Schedule DB
        $schedule = Schedules::where('id',$id)
            ->update([
               'group_result' => $home,
               'opponent_result' => $away
            ]);

        $data = Schedules::where('id',$id)
            ->get();



//        Doing calculation for the scores
         $res = $ws_mygspn->request('POST', env('MYGSPN_API_URL') . '/calculate_group_win_loss',
                    ['body' => json_encode(['real_time_process' => true,
                        'home_team_id' => $data[0]['group_id'],
                        'season_id_home' => $data[0]['season_id_home'],
                        'opponent_id' => $data[0]['event_opponent_id'],
                        'season_id_opponent' => $data[0]['season_id_opponent'],
                        'is_opponent' => false,])]
                )->getBody()->getContents();

         return 'updated';
    }

    private function regex_high_school($word)
    {
        $data = preg_replace('/(.*)\b(High School)\b(.*)/', '$1 HS',$word );
        return $data;
    }
}
