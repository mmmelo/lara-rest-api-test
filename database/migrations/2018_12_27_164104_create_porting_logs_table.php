<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePortingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('porting_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('success')->nullable();
            $table->integer('fail')->nullable();
            $table->integer('total')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user_name')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('porting_logs');
    }
}
