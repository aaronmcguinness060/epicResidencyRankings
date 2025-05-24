<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->string('street_address_1', 100);
            $table->string('street_address_2', 100)->nullable();
            $table->string('town', 50);
            $table->string('county', 50);
            $table->string('country', 50);
            $table->string('eircode', 7);
            $table->timestamps(); // optional, useful for tracking
        });
    }

    public function down(): void {
        Schema::dropIfExists('addresses');
    }
};
