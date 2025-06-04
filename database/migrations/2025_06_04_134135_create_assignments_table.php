<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('residency_id');
            $table->integer('student_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('residency_id')
                ->references('id')->on('residencies')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('student_id')->on('students')
                ->onDelete('cascade');

            // Optional: unique constraint if one student can only have one assignment per residency
            $table->unique(['residency_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
}
