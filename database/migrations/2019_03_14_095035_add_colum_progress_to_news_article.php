<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumProgressToNewsArticle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('news_article'))
        {
            if(Schema::hasColumn('article_progress'))
            {

            }else{
                Schema::table('news_article', function (Blueprint $table) {
                    $table->integer('article_progress')->nullable();
                });
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
