<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();

            // Corrected: single $ sign
            $table->integer('student_id');

            // Foreign key references students.student_id
            $table->foreign('student_id')
                ->references('student_id')
                ->on('students')
                ->onDelete('cascade');

            // Residency foreign key (assuming residencies.id exists)
            $table->foreignId('residency_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Status of the offer
            $table->enum('status', ['pending', 'accepted', 'rejected'])
                  ->default('pending');

            $table->timestamps();

            // Prevent duplicate offers for same user and residency
            $table->unique(['student_id', 'residency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
