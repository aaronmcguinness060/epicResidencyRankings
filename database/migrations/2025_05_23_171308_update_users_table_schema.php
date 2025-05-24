<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            // Rename id to user_id
            $table->renameColumn('id', 'user_id');

            // Drop 'name' column
            $table->dropColumn('name');

            // Modify email length
            $table->string('email', 100)->change();

            // Add new fields
            $table->tinyInteger('user_type')->default(0)->comment('0 = student, 1 = company');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('linkedin_url', 255)->nullable();
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            // Revert changes
            $table->renameColumn('user_id', 'id');
            $table->string('name')->after('id');
            $table->string('email', 255)->change();
            $table->dropColumn(['user_type', 'first_name', 'last_name', 'linkedin_url']);
        });
    }
};
