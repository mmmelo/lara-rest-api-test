<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'news_articles';
    public $primaryKey = 'id';

    protected $fillable =
        [
            'title',
            'author_id',
            'approved',
            'approved_by',
            'deleted',
            'content',
            'deleted_by',
            'headline',
            'topHeadlineDtlinit',
            'topHeadlineDtEnd',
            'top_headline',
            'article_progress'
        ];

    protected $dateFormat = 'U';

    public function media()
    {
        return $this->belongsToMany('App\Media','news_articles_media','article_id','media_id');
    }

}
