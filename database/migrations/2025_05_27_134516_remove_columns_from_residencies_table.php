<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('residencies', function (Blueprint $table) {
            $table->dropColumn(['line_manager_name', 'line_manager_email', 'title']);
        });
    }

    public function down()
    {
        Schema::table('residencies', function (Blueprint $table) {
            // Re-add the columns if you want to rollback (adjust types as needed)
            $table->string('line_manager_name')->nullable();
            $table->string('line_manager_email')->nullable();
            $table->string('title')->nullable();
        });
    }
};
