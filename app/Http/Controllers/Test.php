<?php

namespace App\Http\Controllers;

use App\Media;
use App\NewsArticle;
use App\Http\Controllers\NewsArticle\ArticleMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tag_feeder;
use App\Sports_type;

class Test extends Controller
{
    public function __construct()
    {
        $this->groups = new Groups();
        $this->schedules = new Schedule();
        $this->media_files = new MediaFiles();
        $this->article_media = new ArticleMedia();
    }


    public function index()
    {
        DB::enableQueryLog();
        $data = $this->schedules->find(310318);

        $groups_ids = ['home_id' => $data['group_id'], 'opponent_id' => $data['event_opponent_id']];


        foreach ($groups_ids as $group_id) {
            $this->groups->setMediaFiles($group_id, '1', 310318);
        }

        dd(DB::getQueryLog());

        return $groups_ids;
    }

    public function find_tag()
    {

        $data = Sports_type::with('tags')
            ->where('user_type_id',103)
            ->get();
        return $data;
    }

    public function find_sport()
    {
        DB::enableQueryLog();
        $data = Tag_feeder::with('sports')
            ->where('id',6)
            ->get();

//        dd(DB::getQueryLog());
        return $data;
    }

    public function find_media()
    {
//        $data = Media::with('tags')
//            ->where('id',7246)
//            ->get();
//        return $data;

        $data = Sports_type::whereBetween('user_type_id',[101,199])->get();
        print_r($data);

    }

    public function newsArticle(Request $request)
    {
        $id = $request->get('id');

        DB::enableQueryLog();

        $data = $this->article_media->getArticleMedia($id);

//        dd(DB::getQueryLog()); exit();

        $path ='https://storage.googleapis.com/mygspn-1222.appspot.com/dropzone_upload/IMG_3527_9d1af35cbafee30adf3c92bd9759b4a2.jpg';

        $img = $this->image_to_base64($path);

        return $img;

    }

    private function image_to_base64($path_to_image)
    {
        $type = pathinfo($path_to_image, PATHINFO_EXTENSION);
        $image = file_get_contents($path_to_image);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($image);
        return $base64;
    }

}
