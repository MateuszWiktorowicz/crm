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
        Schema::create('tool_geometries', function (Blueprint $table) {
            $table->id();
            $table->integer('flutes_number');
            $table->integer('diameter');
            $table->integer('face_grinding_time');
            $table->integer('periphery_grinding_times_2d_tool')->nullable();
            $table->unsignedBigInteger('id_tool_type');
            
            $table->foreign('id_tool_type')->references('id')->on('tool_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_geometries');
    }
};
