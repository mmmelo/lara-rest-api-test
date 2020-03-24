<?php

namespace App\Http\Controllers;

use App\Schedules;
use App\Tag_feeder;
use Illuminate\Http\Request;

class Tag_feed extends Controller
{
    public function find(Request $request)
    {
        $id = $request->id;
        $search = $request->param;
        $data = Tag_feeder::with('media')
            ->where('description','LIKE',"%{$search}%")
            ->get();

        return $data;
    }

    //This api would search for a exact match
    public function find_match(Request $request)
    {
        $tag_text = $request->tag;

        $data = Tag_feeder::where('description',$tag_text)
            ->get();

        return $data;
    }

    public function add_new(Request $request)
    {
        $tag_text = $request->tag;
        $tag_option = $request->option;
        $event = $request->event;
        $tag_text_min = strtolower($tag_text);

        $success = false;

        if($tag_option =='no')
        {

            //Pull the Event and find the type of Sport and add to the pivot
            $schedule = Schedules::with('sport_type')
                ->where('id',$event)
                ->get();

            //Confirm if the Tag is not duplicate, if not add it.
            $new_tag = Tag_feeder::firstOrCreate(['description' => $tag_text]);
            $new_tag->description = $tag_text_min;
            if($new_tag->wasRecentlyCreated){
                $success =  true;
            }
            $new_tag->save();
            //Would add the Tag into the pivot table
            $new_tag->sports()->sync(['user_type_name_id' => $schedule[0]['sport_type']['user_type_id']]);
            if($success)
            {
                return (array('status'=>'success','description' => ucwords($tag_text_min),'id' => $new_tag['id']));

            }else{

                return (array('status'=>'error','message'=>'tag already exist in out DB'));
            }



        }else
        {
            $new_tag = Tag_feeder::firstOrCreate(['description' => $tag_text]);
            $new_tag->description = $tag_text_min;
            if($new_tag->wasRecentlyCreated){
                $success =  true;
            }
            $new_tag->save();
            for ($i=100; $i<=200; $i++)
            {
                $new_tag->sports()->attach(['user_type_name_id' => $i]);
            }
            if($success)
            {
                return (array('status'=>'success','description' => ucwords($tag_text_min),'id' => $new_tag['id']));

            }else{

                return (array('status'=>'error','message'=>'tag already exist in out DB'));
            }
        }



    }

    public function list(Request $request)
    {
        $status = true;
        $response = array();


        $search = $request->q;

        $data = Tag_feeder::where('description','like','%'.$search.'%')
            ->get();

        foreach ($data as $i =>$d)
        {
            $response[$i]['id'] = $d['id'];
            $response[$i]['description'] = $d['description'];
        }

        if(empty($response)){
            $status = false;
        };

        return array(
            "status" => $status,
            "error" => null,
            "data" => $response
        );
    }
}
