<?php

namespace App\Http\Controllers\NewsArticle;


use App\NewsArticle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleMedia extends Controller
{
    public function AddMediaToArticle($id,$media)
    {
        $article = NewsArticle::find($id);
        $article->media()->attach($media);

        return response($article);
    }

    public function getArticleMedia($id)
    {
        $article = NewsArticle::with('media')
            ->where('id',$id)
            ->get();

        return response($article[0]['media']);
    }
}
