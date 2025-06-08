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
            $table->unsignedBigInteger('tool_type_id')->nullable()->after('tool_geometry_id');
            $table->foreign('tool_type_id')->references('id')->on('tool_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->dropForeign(['tool_type_id']);
            $table->dropColumn('tool_type_id');
        });
    }
};
