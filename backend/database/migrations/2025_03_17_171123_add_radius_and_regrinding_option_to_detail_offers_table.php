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
        Schema::table('offer_details', function (Blueprint $table) {
            $table->string("regrinding_option");
            $table->float("radius")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_offers', function (Blueprint $table) {
            $table->dropColumn("regrinding_option");
            $table->dropColumn("radius");
        });
    }
};
