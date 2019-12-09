<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('is_absent')->default(0);
            $table->integer('is_requesting')->default(0);
            $table->string('absent_reason', 500)->nullable();
            $table->string('request_reason', 500)->nullable();
            $table->date('date')->nullable();
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance');
    }
}
