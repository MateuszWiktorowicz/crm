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
        if (!Schema::hasTable('offer_details')) {
            Schema::create('offer_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('offer_id');
                $table->unsignedBigInteger('tool_geometry_id');
                $table->integer('tool_quantity');
                $table->decimal('tool_discount', 5, 2);
                $table->decimal('tool_net_price', 10, 2);
                $table->decimal('tool_gross_price', 10, 2);
                $table->unsignedBigInteger('coating_price_id')->nullabe();;
                $table->integer('coating_quantity');
                $table->decimal('coating_discount', 5, 2);
                $table->decimal('coating_net_price', 10, 2);
                $table->decimal('coating_gross_price', 10, 2);
                $table->timestamps();
    
                $table->foreign('offer_id')->references('id')->on('offers')->onDelete('restrict');
                $table->foreign('tool_geometry_id')->references('id')->on('tool_geometries')->onDelete('restrict');
                $table->foreign('coating_price_id')->references('id')->on('coating_prices')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_details');
    }
};
