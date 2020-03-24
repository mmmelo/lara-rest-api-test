<?php

namespace App\Http\Controllers\NewsArticle;

use App\Http\Controllers\ApiController;
use App\NewsArticle;
use Illuminate\Http\Request;

class Articles extends ApiController
{

    public function index(Request $request)
    {
        $limit = $request->get('limit');
        $data = NewsArticle::orderBy('created_at','desc')
            ->take($limit)
            ->get();
        return $this->printData($data);
    }

    public function createArticle(Request $request)
    {
        //Pulling variables//
        $title = $request->get('title');
        $headline = $request->get('headline');
        $article_progress = $request->get('articleProgress');

        if($request->get('event_id'))
        {

        }

        $article = new NewsArticle;

        $article->title = $title;
        $article->headline = $headline;
        $article->article_progress = $article_progress;

        $article->save();

        return $this->printData($article);
    }

    public function find(Request $request)
    {
        $id = $request->get('id');
        $article = NewsArticle::where('id',$id);

        return $this->printData($article);
    }

    public function getMedia(Request $request)
    {

        $article_id = $request->get('id');
        $limit = $request->get('limit');

        //This portion would make the option to limit the result of the media results...
        $article = NewsArticle::with(['media'=> function($query) use ($limit) {
            $query->take($limit);
        }])
            ->where('id',$article_id)
            ->get();

        return $this->printData($article[0]['media']);
    }
}
