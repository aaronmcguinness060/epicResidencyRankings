<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStudentIdForeignKeyOnRankingsTable extends Migration
{
    public function up()
    {
        Schema::table('rankings', function (Blueprint $table) {
            // Drop the existing foreign key constraint on student_id
            $table->dropForeign(['student_id']);

            // Add the new foreign key constraint to reference students.student_id
            $table->foreign('student_id')
                  ->references('student_id')
                  ->on('students')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('rankings', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['student_id']);

            // Revert to original foreign key constraint
            $table->foreign('student_id')
                  ->references('user_id')
                  ->on('students')
                  ->onDelete('cascade');
        });
    }
}
