<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingsTable extends Migration
{
    public function up()
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('residency_id');

            // Position as tiny integer (length 2 not enforced by Laravel, tinyInteger is 1 byte)
            $table->tinyInteger('position');

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('student_id')->references('user_id')->on('students')->onDelete('cascade');
            $table->foreign('residency_id')->references('id')->on('residencies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rankings');
    }
}
