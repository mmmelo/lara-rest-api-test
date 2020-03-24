<?php

namespace App\Http\Controllers;

use App\Group;
use App\Schedules;
use App\Tag_feeder;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Media;
use Illuminate\Support\Facades\DB;


class MediaFiles extends Controller
{

    public function find(Request $request)
    {
        $id = $request->id;
        $data = Media::with('tags')
            ->where('id',$id)
            ->firstOrFail();

        return $data;
    }
    public function saveMediafile($media)
    {
        $new_media = new Media($media);//need to receive the array with values
        $new_media->save();

        return $new_media;
    }

    public function update(Request $request)
    {
        $response = array();
        $pictures = $request->get('data');

        $objects = json_decode($pictures);

        foreach ($objects as $object) {
            $picture = Media::find($object->id);
            $picture->approved = true;
            $picture->sell = $object->sell;
            $picture->rating = $object->rate;
            if(!empty($object->tags))
            {
                $picture->tags()->attach($object->tags);
            }
            $picture->save();

            //Here Im updating the count on the tag usage

            $tags = $object->tags;
            foreach ($tags as $tag)
            {
                $tag_count = Tag_feeder::find($tag);
                $tag_count->count = $tag_count->count + 1;
                $tag_count->save();
            }

            //Remove the opponent picture from the pivot picture
            if($object->highlight)
            {
                DB::enableQueryLog();
                $picture->softDelete_media_pivot($object->highlight,$object->id);
//                dd(DB::getQueryLog());exit;
            }

            $response[] = $picture;
        }

        return $response;

    }

}
