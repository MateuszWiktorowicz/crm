<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coating_types', function (Blueprint $table) {
            $table->id();
            $table->string('mastermet_name', 255);
            $table->string('mastermet_code', 255);
            $table->string('purpose', 255)->nullable();
            $table->string('description', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coating_types');
    }
};
