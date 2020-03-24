<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePivotMediaTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('tag_media_pivot'))
        {
            Schema::create('tag_media_pivot', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('tag_feed_id');
                $table->integer('media_id');
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_media_pivot');
    }
}
