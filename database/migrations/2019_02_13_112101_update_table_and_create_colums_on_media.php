<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableAndCreateColumsOnMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('media'))
        {
            if(!Schema::hasColumn('media','approved','rating','tags','sell'))
            {
                Schema::table('media',function(Blueprint $table)
                {
                   $table->tinyInteger('approved')->nullable();
                   $table->integer('rating')->nullable();
                   $table->integer('tags')->nullable();
                   $table->tinyInteger('sell');
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
        //
    }
}
