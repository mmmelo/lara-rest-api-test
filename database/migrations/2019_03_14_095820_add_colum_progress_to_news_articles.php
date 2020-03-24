<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumProgressToNewsArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('news_articles'))
        {
            if(Schema::hasColumn('news_articles','article_progress'))
            {

            }else{
                Schema::table('news_articles', function (Blueprint $table) {
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
        Schema::table('news_articles', function (Blueprint $table) {
            //
        });
    }
}
