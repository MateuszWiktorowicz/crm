<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateToolGeometryIdNullableInOfferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_details', function (Blueprint $table) {
            // Zmiana kolumny tool_geometry_id na nullable
            $table->unsignedBigInteger('tool_geometry_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_details', function (Blueprint $table) {
            // Przywrócenie kolumny tool_geometry_id jako NOT NULL
            $table->unsignedBigInteger('tool_geometry_id')->nullable(false)->change();
        });
    }
}
