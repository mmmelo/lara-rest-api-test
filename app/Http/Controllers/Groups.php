<?php

namespace App\Http\Controllers;

use App\Media;
use App\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nexmo\Response;


class Groups extends Controller
{



    public function __construct()
    {
    }

    public function index()
    {
        DB::enableQueryLog();
        $data = Group::all();
        dd(DB::getQueryLog());
        return $data;
    }

    public function find(Request $request)
    {
        $id = $request->id;

        $data = Group::with(['schedules','address','media'])
            ->where('group_page_id',$id)
            ->get();

        return $data;
    }

    public function find_with_address($id)
    {

        $data = Group::with('address')
            ->where('group_page_id',$id)
            ->get();

        return $data;
    }

    public function get_slug($group_id)
    {
        $slug = Group::find([$group_id])->first();
        if($slug) {
            return response()->json(array('slug' => $slug['slug']), 200);
        }
        return response()->json(array('No Record Found'),404);

    }
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     *
     * This function would receive the id of the group and save media data inside the media_relation
     */
    public function setMediaFiles($id,$media,$events_id = null)
    {
        $data = Group::find($id);

        if($events_id){
            $data->media()->attach($media,['events_id'=> $events_id]);
        }else{
            $data->media()->attach($media);
        }
        return response()->json(array('Media files added successfully'),200);
    }

    public function search_prediction(Request $request)
    {
        $search_text = $request->search_text;

        if(strlen($search_text) >= 3)
        {
            $data_v2 = array();

            $results = Group::whereHas('players', function($query){
                $query->where('active_page',1);
            })
                ->with('address')
                ->where('full_name','LIKE','%'.$search_text.'%')
                ->where('type_id',2)
                ->get();


            foreach ($results as $result)
            {
                $data['group_id'] = $result['group_page_id'];
                $data['slug'] = $result['slug'];
                $data['full_name'] = $result['full_name'].' | '.$result['address']['user_address_city'].' | '.$result['address']['user_address_state'];
                array_push($data_v2,$data);
            }

            return $data_v2;
        }else{

            return response()->json('not enough param',404);
        }



    }

    public function team_search(Request $request)
    {
        $status = true;
        $response = array();

        $search = $request->q;
        $state_filter = $request->state;
        $sport = $request->sport;

//        $data = Group::with(['schedules','address' => function($query){
//            $query->where('user_address_states','FL');}])
//            ->where('full_name','like','%'.$search.'%')
//            ->get();

        $data = Group::whereHas('address', function($query) use ($state_filter){
            $query->where('user_address_state',$state_filter);
        })
            ->where('type',$sport)
            ->where('full_name','like','%'.$search.'%')
            ->get();

//        foreach ($data as $i => $d){
//
//            $response[$i]['id'] = $d['group_page_id'];
//            $response[$i]['full_name'] = $d['full_name'];
//            $response[$i]['group_level'] = $d['group_level'];
//            $response[$i]['group_gender'] = $d['group_gender'];
//            $response[$i]['state'] = $state_filter;
//        }

        foreach ($data as $i => $d)
        {
            $response[$i]['id'] = $d['group_page_id'];
            $response[$i]['full_name'] = $d['full_name'];
            $response[$i]['group_level'] = $d['group_level'];
            $response[$i]['group_gender'] = $d['group_gender'];
            $response[$i]['state'] = $state_filter;
        }

        if(empty($response)){
            $status = false;
        };
        return array(
            "status" => $status,
            "error" => null,
            "data" => $response
        );


        //-----------------------------------

    }
}
