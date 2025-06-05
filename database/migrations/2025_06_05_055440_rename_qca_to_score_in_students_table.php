<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->renameColumn('qca', 'score');
        });

        // Optionally update the type if necessary (e.g., precision)
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('score', 5, 2)->change(); // Allows values like 100.00
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->renameColumn('score', 'qca');
        });

        // Optionally revert type
        Schema::table('students', function (Blueprint $table) {
            $table->float('qca')->change();
        });
    }
};
