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
        Schema::create('coating_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('diameter');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('id_coating');

            $table->foreign('id_coating')->references('id')->on('coating_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coating_prices');
    }
};
