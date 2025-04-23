<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRegrindingOptionNullableInOfferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_details', function (Blueprint $table) {
            // Zmiana kolumny regrinding_option na nullable
            $table->string('regrinding_option')->nullable()->change();
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
            // Przywrócenie kolumny regrinding_option jako NOT NULL
            $table->string('regrinding_option')->nullable(false)->change();
        });
    }
}
