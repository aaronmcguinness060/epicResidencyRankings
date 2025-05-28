<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixStudentIdTypeInRankingsTable extends Migration
{
    public function up()
    {
        Schema::table('rankings', function (Blueprint $table) {
            // Change column type to unsignedBigInteger
            $table->unsignedBigInteger('student_id')->change();
        });
    }

    public function down()
    {
        Schema::table('rankings', function (Blueprint $table) {
            // Change column back to signed bigInteger or whatever it was
            $table->bigInteger('student_id')->change();
        });
    }
}
